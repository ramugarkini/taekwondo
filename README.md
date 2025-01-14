# Taekwondo Championship Entry System

This project is an online system for managing participant entries for a **Taekwondo Championship**. It allows participants to check if their entry has already been submitted by verifying their name and date of birth. The system is designed to help event organizers streamline the registration process and avoid duplicate entries.

## Features

### 1. **Login System**
- Users can log in to check existing entries or create new ones.
- The system provides a login button, but the login functionality is handled in another part of the system.

### 2. **Individual Entry Form Check**
- Participants can check if their entry has already been submitted by entering their name and date of birth.
- The form sends an AJAX request to the server, checking the database for an existing entry.
- If an entry is found, the user is redirected to their individual entry page.

### 3. **AJAX Data Processing**
- The form uses AJAX to send the data without reloading the page, providing a seamless user experience.
- On success, the user is redirected to their entry page; on failure, an error message is displayed.

### 4. **Error Handling and Notifications**
- Success and error messages are shown dynamically based on the outcome of the AJAX request.
- Error messages are displayed when no entry is found or when there is an issue with the request.

### 5. **Redirection**
- Upon successful entry verification, users are redirected to a page showing their individual entry using the unique ID from the database.

## Why This Project?
This system helps **Taekwondo Championship** organizers efficiently manage participant entries. It reduces manual effort by allowing participants to check for existing entries online. This is particularly useful for event registration, where avoiding duplicate entries and verifying participant details is crucial.


