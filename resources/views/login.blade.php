<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f3f4f6;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background-color: #fff;
        border-radius: 5px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        font-weight: bold;
        display: block;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .form-group button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 3px;
        cursor: pointer;
    }

    .form-group button:hover {
        background-color: #0056b3;
    }

    .error-box {
        color: red;
    }
</style>

<div class="login-container">
    <h2>Login</h2>
    <form id="login-form" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="error-box" id="error-box"></div>
        <button type="submit">Login</button>
        <p for="password">Don't have an account? Create one <span id="register-link" style="color: #0056b3; cursor: pointer;">here</span></p>
    </form>
</div>

<script>
    function setCookie(name, value, days) {
        const expires = new Date()
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000))
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`
    }

    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault()

        const form = e.target;
        const url = "http://localhost:8000/api/login"
        const formData = new FormData(form)

        fetch(url, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                setCookie("auth_token", data.token, 7);
                const responseMessage = document.getElementById('response-message');
                if (data.errors) {
                    const errorBox = document.getElementById('error-box')
                    errorBox.innerHTML = 'Wrong login information.'
                }
                window.location.href = "/"
            })
            .catch(error => {
                const errorBox = document.getElementById('error-box')
                errorBox.innerHTML = 'Wrong login information.'
            })
    })
    document.getElementById('register-link').addEventListener('click', function() {
        window.location.href = '{{ url("/register") }}';
    });
</script>