<?php
/**
 * Configuración de Google Sheets API
 */

// Ruta al archivo de credenciales del Service Account
define('GOOGLE_SERVICE_ACCOUNT_FILE', __DIR__ . '/service-account.json');

// ID de la hoja de cálculo donde se exportarán los datos
// Obtenido de: https://docs.google.com/spreadsheets/d/ESTE_ES_EL_ID/edit
define('GOOGLE_SPREADSHEET_ID', '1dKIpqse0OJwJCvFhFgvbCP0KDtOa1wCN78_3P06bV0g');

// Nombre de la pestaña donde escribir los datos
define('GOOGLE_SHEET_NAME', 'Movimientos');