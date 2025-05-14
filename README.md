Andrei Skyblue - Dynamic PHP Website
=====================================

Description:
-------------
Andrei Skyblue is a dynamic PHP website project that utilizes a custom template engine (MotorPlantilla) to render dynamic content from JSON files. The application separates logic, presentation, and data, making the site easy to update and scale. The project renders pages such as the home page, "Quiénes Somos", contact page, and includes functionalities like email unsubscribing.

Project Structure:
-------------------------
```
C:\xampp\htdocs\andrei-skyblue
├── .gitattributes              # Git configurations.
├── readme.txt                  # Project documentation.
├── inc
│   └── motorplantilla.php      # Custom template engine for dynamic view rendering.
├── index.php                   # Main entry point; handles routing and rendering.
├── json
│   ├── categorias
│   │   └── educacion.json     # Data for the "Educación" category.
│   ├── home.json              # Data for the home page.
│   └── paginas
│       └── quienessomos.json  # Data for the "Quiénes Somos" page.
├── styles.css                  # Main stylesheet with modern and responsive design.
├── templates
│   ├── contacto.html          # Template for the contact page.
│   ├── footer.html            # Template for the footer section.
│   ├── header.html            # Template for the header section.
│   ├── home.html              # Template for the home page.
│   └── quienessomos.html      # Template for the "Quiénes Somos" page.
└── unsuscribe.php              # Script to handle email unsubscribes.
```
Requirements:
--------------
- A web server that supports PHP (e.g., XAMPP).
- PHP 7.0 or higher.

Installation:
--------------
1. Clone or download the project into your working directory (e.g., the htdocs folder of XAMPP).
2. Make sure to maintain the directory structure as provided.
3. Configure your web server to run the project correctly.
4. Verify and adjust file paths in index.php if necessary to match your environment.

Usage:
------
- Home Page: Open your browser and go to "http://localhost/andrei-skyblue/index.php"
- "Quiénes Somos" Page: Navigate to "http://localhost/andrei-skyblue/index.php?pagina=quienessomos"
- Contact Page: Navigate to "http://localhost/andrei-skyblue/index.php?contacto=1"
- Content for products or categories is loaded dynamically based on URL parameters.

Additional Notes:
------------------
- The template engine (MotorPlantilla) processes placeholders within the HTML templates using data from corresponding JSON files.
- The site's appearance is managed by the "styles.css" file, ensuring a consistent and responsive design.
- Check file permissions and server configurations in your local setting to avoid access issues.

Credits:
--------
- Developed by: Andrei Buga

License:
--------
MIT License