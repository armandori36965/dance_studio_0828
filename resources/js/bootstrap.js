import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Livewire
import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";
import Calendar from "../../vendor/guava/calendar/resources/js/calendar.js";
import CalendarContextMenu from "../../vendor/guava/calendar/resources/js/calendar-context-menu.js";
import CalendarEvent from "../../vendor/guava/calendar/resources/js/calendar-event.js";

// Register Alpine components
Alpine.data("calendar", Calendar);
Alpine.data("calendarContextMenu", CalendarContextMenu);
Alpine.data("calendarEvent", CalendarEvent);

// Start Livewire
Livewire.start();
