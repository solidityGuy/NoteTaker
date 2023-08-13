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

    .register-container {
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

<div class="register-container">
    <h2>Register an account</h2>
    <form id="register-form" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="error-box" id="error-box"></div>
        <button type="submit">Create</button>
    </form>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const url = "http://localhost:8000/api/register"
        const formData = new FormData(form)

        fetch(url, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                const responseMessage = document.getElementById('response-message');
                console.log(data)
                if (data.errors) {
                    const errorBox = document.getElementById('error-box')
                    errorBox.innerHTML = 'Invalid information.'
                }
            })
            .catch(error => {
                const errorBox = document.getElementById('error-box')
                errorBox.innerHTML = 'Invalid information.'
            })
        })
</script>