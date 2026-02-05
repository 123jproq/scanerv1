# 📦 Sistema de Inventario con Scanner

Sistema completo de gestión de inventario con aplicación web administrativa y PWA móvil para escaneo de códigos de barras.

## 🚀 Características

### Panel Administrativo
- ✅ Gestión de productos con imágenes
- ✅ Sistema de categorías y áreas
- ✅ Control de stock con historial de movimientos
- ✅ Registro de entradas y salidas
- ✅ Reportes y exportación a Excel/Google Sheets
- ✅ Interfaz moderna con DataTables

### PWA Móvil
- 📱 Escaneo de códigos de barras con cámara
- 📱 Registro rápido de productos
- 📱 Salidas de stock por área
- 📱 Funciona offline
- 📱 Instalable como aplicación

## 🛠️ Tecnologías

- **Backend**: PHP 7+ con MySQLi
- **Frontend**: HTML5, CSS3, JavaScript
- **Librerías**: jQuery, Bootstrap 3, DataTables
- **PWA**: Service Workers, Manifest.json
- **Integraciones**: Google Sheets API, PHPExcel

## 📋 Requisitos

- PHP 7.0 o superior
- MySQL 5.7 o superior
- Servidor Apache (XAMPP recomendado)
- Navegador moderno con soporte para PWA

## ⚙️ Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/TU_USUARIO/scaner.git
cd scaner
```

2. **Configurar la base de datos**
```bash
# Importar el archivo SQL
mysql -u root -p < database/stock_movements.sql
```

3. **Configurar conexión a BD**
Editar `php_action/db_connect.php`:
```php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'stock';
$store_url = "http://localhost/scaner/";
```

4. **Configurar carpeta de imágenes**
```bash
# Dar permisos de escritura
chmod 777 assests/images/stock/
```

5. **Acceder al sistema**
- Panel Admin: `http://localhost/scaner/`
- PWA: `http://localhost/scaner/pwa/`

## 👤 Usuario por Defecto

- **Usuario**: admin
- **Contraseña**: (configurar en la BD)

## 📱 Instalación de PWA en Móvil

1. Abrir `http://TU_SERVIDOR/scaner/pwa/` en el navegador móvil
2. Hacer clic en "Agregar a pantalla de inicio"
3. La app quedará instalada como aplicación nativa

## 📊 Estructura del Proyecto

```
scaner/
├── assests/          # CSS, JS, imágenes
├── config/           # Configuraciones (Google Sheets, etc.)
├── custom/           # JavaScript personalizado
├── database/         # Scripts SQL
├── includes/         # Header, footer, navegación
├── libraries/        # PHPExcel y otras librerías
├── php_action/       # Lógica backend PHP
├── pwa/              # Progressive Web App
├── dashboard.php     # Panel principal
├── index.html        # Página de login
└── README.md         # Este archivo
```

## 🔐 Seguridad

- **Autenticación**: Sistema de sesiones PHP
- **SQL Injection**: Validación de datos (¡mejorar con prepared statements!)
- **Archivos sensibles**: Excluidos en `.gitignore`

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/mejora`)
3. Commit tus cambios (`git commit -m 'Añadir nueva característica'`)
4. Push a la rama (`git push origin feature/mejora`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es de código abierto.

## 📧 Contacto

Para consultas o soporte, abrir un issue en GitHub.

---

⭐ Si te gusta el proyecto, dale una estrella en GitHub!
