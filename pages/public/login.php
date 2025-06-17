<section id="login" class="login-section">
  <div class="login-container">
    <h2>Welcome Back</h2>
    <p class="login-subtext">Sign in to access your dashboard.</p>

    <form class="login-form" action = "./server/backend/login.php" method = "POST">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required>
      </div>

      <div class="form-group">
        <label for="password">Your Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="login-btn">Login</button>

      <p class="login-extra">
        Don’t have an account? <a href = "?createAccount">Create one here</a>
      </p>
    </form>
  </div>
</section>
