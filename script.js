$(document).ready(function() {
    var currentPage = 1;
    var currentSortBy = 'created_at';
    var currentSortOrder = 'DESC';

    function loadMessages(page, sortBy, sortOrder) {
        $.ajax({
            type: "GET",
            url: "submit.php",
            data: { page: page, sort_by: sortBy, sort_order: sortOrder },
            dataType: "json",
        }).done(function(data) {
            if (data.success) {
                $("#messages").empty();
                data.messages.forEach(function(message) {
                    $("#messages").append(
                        `<tr>
                            <td>${message.user_name}</td>
                            <td>${message.email}</td>
                            <td>${message.message}</td>
                            <td>${message.created_at}</td>
                        </tr>`
                    );
                });
                updatePagination(data.total_pages, page);
            } else {
                alert("Error: " + data.error);
            }
        }).fail(function(xhr, status, error) {
            alert("An error occurred: " + xhr.responseText);
        });
    }

    function updatePagination(totalPages, currentPage) {
        $(".pagination").empty();
        for (var i = 1; i <= totalPages; i++) {
            $(".pagination").append(
                `<a href="#" class="${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`
            );
        }

        $(".pagination a").click(function(event) {
            event.preventDefault();
            var page = $(this).data("page");
            loadMessages(page, currentSortBy, currentSortOrder);
        });
    }

    $("th.sortable").click(function() {
        var sortBy = $(this).data("sort-by");
        var sortOrder = $(this).hasClass("asc") ? "DESC" : "ASC";
        currentSortBy = sortBy;
        currentSortOrder = sortOrder;
        loadMessages(currentPage, sortBy, sortOrder);
        $("th.sortable").removeClass("asc desc");
        $(this).addClass(sortOrder.toLowerCase());
    });

    $("#guestbook-form").submit(function(event) {
        event.preventDefault();

        if (!validateForm()) {
            return;
        }

        var formData = {
            user_name: $("#user_name").val(),
            email: $("#email").val(),
            message: $("#message").val(),
            captcha: $("#captcha").val()
        };

        $.ajax({
            type: "POST",
            url: "submit.php",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function(data) {
            if (data.success) {
                loadMessages(currentPage, currentSortBy, currentSortOrder);
                $("#guestbook-form")[0].reset();
                refreshCaptcha();
            } else {
                switch (data.error) {
                    case "Invalid user name":
                        $('#user_name').parent().addClass('error');
                        break;
                    case "Invalid email":
                        $('#email').parent().addClass('error');
                        break;
                    case "Message is required":
                        $('#message').parent().addClass('error');
                        break;
                    case "Invalid CAPTCHA":
                        $('#captcha').parent().addClass('error');
                        break;
                    default:
                        alert("An error occurred: " + xhr.responseText);
                }
            }
        }).fail(function(xhr, status, error) {
            alert("An error occurred: " + xhr.responseText);
        });
    });

    function validateForm() {
        $('#guestbook-form input, #guestbook-form textarea').each(function() {
            $(this).parent().removeClass('error');
        })

        var userName = $("#user_name").val();
        var email = $("#email").val();
        var message = $("#message").val();
        var captcha = $("#captcha").val();
        var isValid = true;

        if (!/^[a-zA-Z0-9]+$/.test(userName)) {
            $('#user_name').parent().addClass('error');
            isValid = false;
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            $('#email').parent().addClass('error');
            isValid = false;
        }

        if (message.trim() === "") {
            $('#message').parent().addClass('error');
            isValid = false;
        }

        if (!captcha) {
            $('#captcha').parent().addClass('error');
            isValid = false;
        }

        return isValid;
    }

    function refreshCaptcha() {
        $("#captcha-img").attr("src", "captcha.php?" + new Date().getTime());
    }

    loadMessages(currentPage, currentSortBy, currentSortOrder);
    refreshCaptcha();
});
