<?php

namespace App\Controller\AdminFlock;

use App\Event\IotQuizEvents;
use App\Event\LongPollingLastModificationCheckEvent;
use App\Service\AdminFlock\LayoutStatusInfo;
use App\Service\AdminFlock\TaxCodeCaluculator;
use Geocoder\Exception\Exception;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin", name="utility")
 */
class UtilityController extends AbstractController
{
    /**
     * @Route("/geocode", name="_geocode", options = { "expose" = true },)
     * @param Request $request
     * @param Provider $googleMapsGeocoder
     * @return Response
     * @throws Exception
     */
    public function geocode(Request $request, Provider $googleMapsGeocoder)
    {
        $geocoder = $googleMapsGeocoder;

        $term = $request->query->get('term');
        $fullAddress = $request->query->get('fullAddress');

        $address = array();

        if ($fullAddress) {
            $results = $geocoder->geocodeQuery(GeocodeQuery::create($fullAddress));

            if ($results) {
                foreach($results as $result) {
                    foreach ($result->getAdminLevels() as $key => $level) {
                        $address['administrative_area_level_'.$key] = $level->getCode();
                    }

                    $address['street_number'] = $result->getStreetNumber();
                    $address['route'] = $result->getStreetName();
                    $address['postal_code'] = $result->getPostalCode();
                    $address['locality'] = $result->getLocality();
                    $address['country'] = $result->getCountry()->getName();
                    $address['formattedAddress'] = $result->getFormattedAddress();
                    $address['latitude'] = $result->getCoordinates()->getlatitude();
                    $address['longitude'] = $result->getCoordinates()->getlongitude();

                }
            }

            if (!isset($address['locality']) && isset($address['administrative_area_level_3'])) {
                $address['locality'] = $address['administrative_area_level_3'];
            }

        } else if ($term) {
            $results = $geocoder->geocodeQuery(GeocodeQuery::create($term));

            foreach ($results as $key => $result) {
                $address[] = [
                    'value' => $result->getformattedAddress(),
                    'city' => $result->getLocality(),
                    'country' => $result->getCountry()->getName()
                ];
            }
        }

        $response = new Response();
        $response->setContent(json_encode($address));

        return $response;
    }

    public function objectListAction(Request $request)
    {
        $objectList = array();

        $class = $request->query->get('class');
        //$class = 'Sinervis\QualityBundle\Entity\Reports\RM\InternalIssue';

        if ($class) {
            list($bundleName, $entityName) = explode('\\Entity\\', $class);
            $bundleName = str_replace('\\', '', $bundleName);

            $em = $this->getDoctrine()->getManager();
            $results = $em->getRepository($bundleName . ':' . $entityName)->findAll();
            foreach ($results as $value) {
                $objectList[] = (string)$value;
            }
        }

        $response = new Response();
        $response->setContent(json_encode($objectList));

        return $response;
    }

    // Todo: generalize this function
    public function singleAttachmentDownloadAction($id)
    {
        $this->preExecute();

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('SinervisContactBundle:Roles/Teacher')
            ->createQueryBuilder('a')
            ->leftJoin('a.teacher', 'e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $attachment = $query->getOneOrNullResult();
        $file_path = $attachment->getFile()->getPathname();
        $file_name = $attachment->getName();
        $this->attachmentDownload($attachment, $file_path, $file_name);
    }


    public function fileDownloadAction($id, $class_name, $file_field, $file_name)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($class_name)->find($id);
        $file = $entity->{"get$file_field"}();

        $downloadHandler = $this->get('vich_uploader.download_handler');

        $response = $downloadHandler->downloadObject($entity, $file_field, $objectClass = null, $file_name);
        $response->headers->set('Content-Disposition', 'inline');
        $response->headers->set('Content-Type', $file->getMimeType());
        return $response;
    }


    /**
     * List entity filtered by property specified in autocomplete_filters.yml in app/config
     *
     * @param Array $param array( 'starting_table' => 'tablename',
     *                            'join' => array( 'table2' , 'table3' ),
     *                            'property' => 'fullNameCanonical',
     *                            'class' => 'Sinervis\ContactBundle\Entity\Roles\Customer'
     *                             )
     * @param Request $request
     *
     * @return Response or null if parameters missing
     */
    public function genericAjaxListAction($filterid, Request $request)
    {
        //get filter autocomplete configurations
        $param = $this->container->getParameter('autocomplete_configs');
        //if the key defined by filterid is not set return null
        if (!isset($param['autocomplete'][$filterid])) {
            return;
        }
        //else redefine $param
        $param = $param['autocomplete'][$filterid];

        //check if property and starting table keys exists
        if (!isset($param['starting_table']) || !isset($param['property']) || !isset($param['class'])) {
            return;
        }
        $objects = $this->getDoctrine()->getManager()
            ->getRepository($param['class'])
            ->createQueryBuilder($param['starting_table']);

        //do join if needed
        $base_table = $param['starting_table'];
        foreach ($param['join'] as $join_table) {
            $objects
                ->leftJoin($base_table . '.' . $join_table, $join_table);
            $base_table = $join_table;
        }

        //add where
        if ($term = $request->get('term')) {
            //if param['join'] is set use last item for where clausole else use $param['starting_table']
            if (isset($param['join']) && !empty($param['join'])) {
                end($param);
                $param['starting_table'] = current($param['join']);
            }
            $objects
                ->where($objects->expr()->like($param['starting_table'] . '.' . $param['property'], ':parameter'))
                ->setParameter('parameter', '%' . $term . '%');
        }


        $objects = $objects->getQuery()->getResult();

        $json = array();
        foreach ($objects as $object) {
            $json[] = array(
                'label' => $object->__toString(),
                'value' => $object->getId(),
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }

    /**
     * @Route("/get-tax-code", name="_get_tax_code", options = { "expose" = true })
     * @param Request $request
     * @param TaxCodeCaluculator $taxCodeCaluculator
     * @return JsonResponse
     */
    public function taxCodeAction(Request $request, TaxCodeCaluculator $taxCodeCaluculator)
    {
        $params = $request->get('tax_code_params');
        $cf = $taxCodeCaluculator->calculate($params);

        $response = new JsonResponse($cf);

        return $response;
    }

    /**
     * @Route("/long-poll", name="_long_poll", options = { "expose" = true })
     */
    public function longPoll(EventDispatcherInterface $eventDispatcher)
    {
        session_write_close();
        set_time_limit(0);

        $modifications = [];
        $event = new LongPollingLastModificationCheckEvent(time(), $modifications);

        while (true) {
            $eventDispatcher->dispatch($event, IotQuizEvents::LAST_MODIFICATION_API);
            $modifications = $event->getData();

            if (!empty($modifications)) {
                return new JsonResponse($modifications);
            }

            sleep( 3);
            continue;
        }
    }
}