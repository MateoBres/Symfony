
import 'bootstrap';
import 'flatpickr';
import './../sinervis/sv_form_helper';
import './../sinervis/_javascript_collection_widget';
import runAllForms from './../sinervis/form-plugin-initialization';
import '../sinervis/components/image-crop';
runAllForms();

import './../sinervis/components/geocode-full-address';



// multistep form wizard
// import './../sinervis/sv_jquery.stepy';
// if ($('div.stepy-tab').length) {
//     $('#admin-form').stepy({
//         backLabel: 'Precedente',
//         block: true,
//         nextLabel: 'Prossimo',
//         titleClick: true,
//         titleTarget: '.stepy-tab'
//     });
// }