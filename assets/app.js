/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

import Alert from 'bootstrap/js/dist/alert';

// start the Stimulus application
import './bootstrap';

import { createIcons, ArrowDown, Building, MapPin, Tags, Pin, Briefcase } from 'lucide';
createIcons({
    icons: {
        ArrowDown,
        Building,
        MapPin,
        Tags,
        Pin,
        Briefcase
    },
    attrs: {
        width: 20,
        height: 20,
    }
});
