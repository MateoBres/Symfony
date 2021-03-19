<?php

namespace App\Entity\ContactFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;
use Ivory\GoogleMap\Services\Geocoding\GeocoderProvider;
use Ivory\GoogleMap\Services\Geocoding\GeocoderRequest;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Events\MouseEvent;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Sinervis\ContactBundle\Entity\ContactInfos\Email;
use Sinervis\ContactBundle\Entity\ContactInfos\Phone;


/**
 * Place
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *   "Place"    = "Place",
 *   "House"    = "App\Entity\ContactFlock\Places\House",
 *   "Office"   = "App\Entity\ContactFlock\Places\Office",
 * })
 */
class Place implements TimestampableInterface
{
    use TimestampableTrait;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_address", type="string", length=255)
     *
     * @Assert\NotBlank(message="Indirizzo completo non dovrebbe essere vuoto.")
     */
    protected $fullAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    protected $street;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    protected $number;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)
     */
    protected $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    protected $province;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     */
    protected $region;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="geocode_status", type="string", length=255, nullable=true)
     */
    protected $geocodeStatus;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="string", length=255, nullable=true)
     */
    protected $siteName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContactFlock\Contact", inversedBy="ownedPlaces", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    protected $contact;

    /**
     * @ORM\ManyToMany(targetEntity="ContactInfo", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinTable(name="places_contactinfo",
     *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contactinfo_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
     *      )
     * @Assert\Valid()
     */
    private $infos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_fiscal_sending_address", type="boolean", nullable=true, options={"default"=0})
     */
    private $isFiscalSendingAddress;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __construct()
    {
        $this->isFiscalSendingAddress = false;
    }

    public function __toString()
    {
        return $this->fullAddress;
    }

    public function getFormattedAddress()
    {
        $addressParts = [];
        $addressParts[] = $this->street;
        $addressParts[] = $this->number;
        $addressParts[] = $this->city;

        if ($this->province) {
            $addressParts[] = '('.$this->province.')';
        }

        $addressParts = array_filter($addressParts);
        return implode(' ', $addressParts);
    }

    public function getIcon()
    {
        return 'build/images/sinervis/ContactFlock/place.png';
    }

    public function getMap()
    {
        $map = new Map();

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');

        $map->setAsync(false);
        $map->setAutoZoom(false);

        $map->setCenter((double)$this->getLatitude(), (double)$this->getLongitude(), true);
        $map->setMapOption('zoom', 10);

        $map->setBound(-2.1, -3.9, 2.6, 1.4, true, true);

        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('mapTypeId', 'roadmap');

        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('disableDoubleClickZoom', true);
        $map->setMapOptions(array(
            'disableDefaultUI' => true,
            'disableDoubleClickZoom' => true,
        ));

        $map->setStylesheetOption('width', '100%');
        $map->setStylesheetOption('height', '300px');
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px',
        ));

        $map->setLanguage('it');

        $marker = new Marker();

        // Configure your marker options
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition((double)$this->getLatitude(), (double)$this->getLongitude(), true);
        $marker->setAnimation(Animation::DROP);

        $marker->setOption('clickable', false);
        $marker->setOption('flat', true);
        $marker->setOptions(array(
            'clickable' => false,
            'flat' => true,
        ));
        $map->addMarker($marker);

        $marker->setIcon($this->getIcon());


        return $map;
    }

    public function getMarkerInfoWindow()
    {
        $infoWindow = new InfoWindow();
        $infoWindow->setContent('');
        $infoWindow->setOpen(false);
        $infoWindow->setAutoOpen(true);
        $infoWindow->setOpenEvent(MouseEvent::CLICK);
        $infoWindow->setAutoClose(true);
        $infoWindow->setOptions(array(
            //'disableAutoPan' => true,
            'zIndex' => 10,
            'borderRadius' => 15,
        ));

        return $infoWindow;
    }

    public function doGeocode()
    {
        $geocoder = new Geocoder();
        $geocoder->registerProviders(array(
            new GeocoderProvider(new CurlHttpAdapter()),
        ));

        $request = new GeocoderRequest();
        $request->setLanguage('it');
        $request->setAddress($this->fullAddress);

        $response = $geocoder->geocode($request);
        $this->setGeocodeStatus($response->getStatus());
        $results = $response->getResults();
        if (count($results)) {
            $result = $results[0];
            $this->setFullAddress($result->getformattedAddress(), false);
            $this->setStreet($this->getGeoComponent($result, 'route'));
            $this->setNumber($this->getGeoComponent($result, 'street_number'));
            $this->setZip($this->getGeoComponent($result, 'postal_code'));
            $this->setCity($this->getGeoComponent($result, 'administrative_area_level_3')); // 'locality' is more reliable than 'administrative_area_level_3'
            $this->setProvince($this->getGeoComponent($result, 'administrative_area_level_2', 'getShortName'));
            $this->setRegion($this->getGeoComponent($result, 'administrative_area_level_1'));
            $this->setCountry($this->getGeoComponent($result, 'country'));

            $coord = $result->getGeometry()->getLocation();
            $this->setLatitude($coord->getLatitude());
            $this->setLongitude($coord->getLongitude());

            return true;
        } else {
            return false;
        }
    }

    private function getGeoComponent($geo, $name, $getter = 'getLongName')
    {
        $res = $geo->getAddressComponents($name);
        return count($res) ? $res[0]->$getter() : '';
    }

    public function getType()
    {
        return 'Luogo';
    }

    public function getPlacePhones()
    {
        $phones = array();
        foreach ($this->getContactable()->getInfos() as $info) {
            if ((new \ReflectionClass($info))->getShortName() == 'Phone') {
                $phones[] = $info->getValue();
            }
        }
        return implode(', ', $phones);
    }

    /**
     * @Assert\Callback
     */
    public function isPhoneTypeSelected(ExecutionContextInterface $context)
    {
        //Check if type is selected if the entity is type of Phone.
//        foreach ($this->getContactable()->getInfos() as $key => $info) {
//            $value = $info->getValue();
//            $type = $info->getType();
//            $errorMsg = null;
//            if (empty($value)) {
//                $errorMsg = 'Questo valore non dovrebbe essere vuoto';
//            } elseif ($info instanceof Phone && is_null($type)) {
//                $errorMsg = 'Scegli il tipo';
//            }
//
//            if (!is_null($errorMsg)) {
//                $context->buildViolation($errorMsg)
//                    ->atPath('contactable.infos[' . $key . '].value')
//                    ->addViolation();
//            }
//        }
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullAddress
     *
     * @param string $fullAddress
     * @return Place
     */
    public function setFullAddress($fullAddress, $format = false)
    {
        if ($format) {
            $this->fullAddress = ucwords(strtolower($fullAddress));
        } else {
            $this->fullAddress = $fullAddress;
        }

        return $this;
    }

    /**
     * Get fullAddress
     *
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Place
     */
    public function setStreet($street)
    {
        $this->street = ucwords(strtolower($street));

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Place
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Place
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }


    /**
     * Set city
     *
     * @param string $city
     * @return Place
     */
    public function setCity($city)
    {
        $this->city = ucwords(strtolower($city));

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set province
     *
     * @param string $province
     * @return Place
     */
    public function setProvince($province)
    {
        $this->province = strtoupper($province);

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return Place
     */
    public function setRegion($region)
    {
        $this->region = ucwords(strtolower($region));

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Place
     */
    public function setCountry($country)
    {
        $this->country = ucwords(strtolower($country));

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set geocodeStatus
     *
     * @param string $geocodeStatus
     * @return Place
     */
    public function setGeocodeStatus($geocodeStatus)
    {
        $this->geocodeStatus = $geocodeStatus;

        return $this;
    }

    /**
     * Get geocodeStatus
     *
     * @return string
     */
    public function getGeocodeStatus()
    {
        return $this->geocodeStatus;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Place
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Place
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set siteName
     *
     * @param string $siteName
     * @return Place
     */
    public function setSiteName($siteName)
    {
        $this->siteName = ucwords(strtolower($siteName));

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Set contact
     *
     * @param \App\Entity\ContactFlock\Contact $contact
     * @return Place
     */
    public function setContact(\App\Entity\ContactFlock\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \Sinervis\ContactBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set isFiscalSendingAddress
     *
     * @param boolean $isFiscalSendingAddress
     *
     * @return Place
     */
    public function setIsFiscalSendingAddress($isFiscalSendingAddress)
    {
        $this->isFiscalSendingAddress = $isFiscalSendingAddress;

        return $this;
    }

    /**
     * Get isFiscalSendingAddress
     *
     * @return boolean
     */
    public function getIsFiscalSendingAddress()
    {
        return $this->isFiscalSendingAddress;
    }

    /**
     * Add info
     *
     * @param \App\Entity\ContactFlock\ContactInfo $contactInfo
     * @return Contact
     */
    public function addInfo(\App\Entity\ContactFlock\ContactInfo $contactInfo)
    {
        $this->infos[] = $contactInfo;
        return $this;
    }

    /**
     * Remove infos
     *
     * @param \App\Entity\ContactFlock\ContactInfo $infos
     */
    public function removeInfo(\App\Entity\ContactFlock\ContactInfo $contactInfo)
    {
        $this->infos->removeElement($contactInfo);
    }

    /**
     * Get infos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**************************************/
    /* END                                */
    /**************************************/


}
