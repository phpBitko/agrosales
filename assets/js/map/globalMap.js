bootbox = require('bootbox');
mapSales = undefined;

require('jquery-ui/ui/widgets/draggable');
require('../lib/jQueryPreloader/src/js/jquery.preloader.min');
require('../lib/jQueryPreloader/src/css/preloader.css');
require('../../css/map/map.scss');
require('../../js/lib/adminLTE/js/adminlte');
require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.uk');
require('bootstrap-slider/dist/bootstrap-slider');
require('inputmask/dist/jquery.inputmask.bundle'); //маска для ведення значень фільтру


bootboxMessage = require('../../js/functionLib/bootboxMessage');
bootboxAlertMessage = require('../../js/functionLib/bootboxAlertMessage');
getLayerByName = require('../../js/functionLib/getLayerByName');

require('../../js/map/initialLayers');
require('../../js/map/initialToolbar');
featureMapControl = require('../../js/functionLib/featureManipulations');
serviceFunction = require('../../js/functionLib/serviceFunction');
require('../../js/map/addAdvertisement');
require('../../js/map/visualization');
require('../../js/map/zoomToPoint');
require('../../js/map/filter');

