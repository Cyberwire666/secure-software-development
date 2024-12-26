<?php
// about.php

// Start session (if needed for header navigation)
session_start();

// Optional: Include log helper or other necessary functions
require_once '../helpers/log_helper.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Notes App</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to external stylesheet -->
    <style>
        /* Centered List Style */
ul {
    list-style: none; /* Remove default bullets */
    margin: 0 auto; /* Center the list block horizontally */
    padding: 0; /* Remove extra padding */
    display: flex; /* Enable flexbox for alignment */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically if needed */
    gap: 15px; /* Add spacing between items */
}

ul li {
    text-align: center; /* Center-align individual list item content */
}

ul li a {
    padding: 8px 12px; /* Adjust padding for clickable area */
    border-radius: 4px; /* Rounded corners */
    transition: background-color 0.3s ease;
}

ul li a:hover {
    background-color: #f1f1f1; /* Optional hover effect */
}

    </style>
</head>
<body>

<?php include('../templates/header.php'); // Include the reusable header ?>

<div class="container">
    <h1>About Notes App</h1>

    <section class="about-purpose">
        <h2>Purpose of Notes App</h2>
        <p>Notes App is a secure and efficient platform designed to help users manage their notes and tasks effortlessly. Whether you're a student, professional, or just someone who loves staying organized, Notes App is here to support you.</p>
    </section>

    <section class="about-features">
        <h2>Features</h2>
        <ul>
            <li>Secure login with JWT-based authentication.</li>
            <li>Two-factor authentication (OTP) for enhanced security.</li>
            <li>Create, update, and delete notes effortlessly.</li>
            <li>Responsive and user-friendly design across all devices.</li>
        </ul>
    </section>

    <section class="about-team">
        <h2>Meet the Team</h2>
        <p>We are a passionate group of developers dedicated to creating intuitive and secure software solutions. Our goal is to help you focus on what matters by removing the hassle of managing your notes.</p>
        <p><strong>Lead Developer:</strong>  Yehia Tarek</p>
        <p><strong>Team:</strong><br>Sara Ahmed <br>Shadwa Ahmed <br>Marawan Farouk</p>
    </section>

    <section class="about-contact">
        <h2>Contact Us</h2>
        <p>Got questions or feedback? Reach out to us anytime at <a href="mailto:yehiaselim16@gmail.com">yehiaselim16@gmail.com</a>.</p>
    </section>
</div>

<?php include('../templates/footer.php'); // Include the reusable footer ?>

</body>
</html>
