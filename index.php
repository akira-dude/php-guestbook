<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script.js"></script>
</head>
<body>
    <div class="form-container">
        <h2>Add a new message</h2>
        <form id="guestbook-form" method="POST">
            <div class="guestbook-form__input-group">
                <label for="user_name">User Name: <span>*<i>invalid</i></span></label>
                <input type="text" id="user_name" name="user_name" required value="name">
            </div>
            <div class="guestbook-form__input-group">
                <label for="email">E-mail: <span>*<i>invalid</i></span></label>
                <input type="email" id="email" name="email" required value="test@test.ru">
            </div>
            <div class="guestbook-form__input-group">
                <label for="message">Message: <span>*<i>required</i></span></label>
                <textarea id="message" name="message" required>massage</textarea>
            </div>
            <div class="guestbook-form__input-group captcha">
                <label for="captcha">CAPTCHA <span>*<i>required</i></span></label>
                <input type="text" id="captcha" name="captcha" required>
                <img id="captcha-img" src="captcha.php" alt="CAPTCHA">
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="messages">
        <h2>Messages</h2>
        <table>
            <thead>
                <tr>
                    <th class="sortable" data-sort-by="user_name">User Name <i>a-z&#8595;</i></th>
                    <th class="sortable" data-sort-by="email">E-mail <i>a-z&#8595;</i></th>
                    <th>Message</th>
                    <th class="sortable" data-sort-by="created_at">Date <i>0-9&#8595;</i></th>
                </tr>
            </thead>
            <tbody id="messages"></tbody>
        </table>
    </div>

    <div class="pagination"></div>
</body>
</html>
