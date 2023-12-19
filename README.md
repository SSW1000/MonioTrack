#  ![MonioTrack Logo](./images/mt.png "MonioTrack Logo") MonioTrack 


MonioTrack is a comprehensive expense and income tracking web application designed to help users manage their finances efficiently. This application allows users to conveniently track their expenses, income, and bank accounts.

## Features

- **User Authentication:** Users can create accounts, log in securely, and access their financial data.
- **Expense Tracking:** Allows users to add, view, and manage their expenses, including details like amount, category, date, and spending method.
- **Income Management:** Users can record their income, view it, and track their overall earnings.
- **Bank Account Management:** Provides functionality to manage bank accounts, including adding new accounts, viewing account details, and updating balances.
- **Currency Preferences:** Users can set their preferred currency for displaying amounts.
- **Data Visualization:** Utilizes Chart.js to present graphical representations of financial data for better analysis.
- **Responsive Design:** The application is designed to be responsive and accessible across various devices.

## Getting Started

To run MonioTrack locally, follow these steps:

### Prerequisites

- **Web Server:** Apache (v2.4) or Nginx (v1.21) or any other web server software.
- **Database:** MySQL (v5.7) or MariaDB (v10.5) to store user and financial data.
- **PHP:** Version 7.4 or higher.
- **Browser:** Google Chrome, Mozilla Firefox, Safari, or any modern browser.

### Installation

1. Clone the repository: `git clone https://github.com/SSW1000/MonioTrack.git`
2. Import the database schema using the provided SQL file (`sql/iet.sql`).
3. Update the `includes/connection.php` file with your database credentials (follow the instructions below).
4. Place the project files in the root directory of your web server.

### Updating connect.php with Database Credentials

1. Locate the `connection.php` file in the includes directory.
2. Open `connection.php` in a text editor or an IDE like Visual Studio Code.
3. Update the following variables in `connection.php` with your database connection details:

   ```php
   $servername = "localhost"; // Change this to your MySQL server's hostname
   $username = "your_username"; // Change this to your MySQL username
   $password = "your_password"; // Change this to your MySQL password
   $dbname = "iet"; // Change this to your MySQL database name

   // Create a connection
   $db = mysqli_connect($servername, $username, $password, $dbname);

   // Check the connection
   if (!$db) {
       die("Connection failed: " . mysqli_connect_error());
   }
   ```

   - Replace `"localhost"` with your MySQL server's hostname or IP address.
   - Replace `"your_username"` with your MySQL username.
   - Replace `"your_password"` with your MySQL password.
   - Change `"iet"` to the name of your MonioTrack database.

4. Save the changes made to `connection.php`.

### Usage

1. Access the application through your web server (`http://localhost/MonioTrack`).
2. Sign up for a new account or log in with existing credentials.
3. Navigate through the dashboard to manage expenses, income, and bank accounts.

## Technologies Used

- **Frontend:** 
  - HTML5 ![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
  - CSS3 ![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
  - JavaScript (ES6) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
- **Backend:** 
  - PHP (v7.4) ![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
- **Database:** 
  - MySQL (v5.7) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
- **Frameworks/Libraries:**
  - [Bootstrap](https://getbootstrap.com/) (v4.5.2) - Frontend component library ![Bootstrap](https://img.shields.io/badge/Bootstrap-v4.5.2-blue?logo=bootstrap)
  - [jQuery](https://jquery.com/) (v3.5.1) - JavaScript library for DOM manipulation ![jQuery](https://img.shields.io/badge/jQuery-v3.5.1-blue?logo=jquery)
  - [DataTables](https://datatables.net/) (v1.13.7) - jQuery plugin for table manipulation ![DataTables](https://img.shields.io/badge/DataTables-v1.13.7-blue?logo=jquery)
  - [Font Awesome](https://fontawesome.com/) (v5.15.4) - Icon library ![Font Awesome](https://img.shields.io/badge/Font%20Awesome-v5.15.4-blue?logo=font-awesome)
  - [SweetAlert2](https://sweetalert2.github.io/) (v11.0.22) - Library for beautiful, responsive alerts ![SweetAlert2](https://img.shields.io/badge/SweetAlert2-v11.0.22-blue?logo=javascript)
  - [Chart.js](https://www.chartjs.org/) (v3.7.0) - JavaScript library for creating charts and graphs ![Chart.js](https://img.shields.io/badge/Chart.js-v3.7.0-blue?logo=chart.js)


## Contributing

Contributions are welcome! If you have any suggestions, feature requests, or bug reports, please submit an issue or create a pull request.

## Acknowledgements

Special thanks to the developers and communities behind the libraries and frameworks used in this project.

## Author

Supun Sandeepa Wimalarathne - [GitHub Profile](https://github.com/SSW1000)

---

