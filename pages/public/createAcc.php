<section id="create-account" class="create-account-section">
  <div class="account-container">
    
    <div class="account-text">
      <h2>Get Started for Free</h2>
      <p class="create-subtext">Create your team account and start collaborating in minutes.</p>
    </div>

    <form class="account-form" action="./server/backend/createAcc.php" method="POST">
      
      <div class="form-group">
        <label for="company">Team or Company Name</label>
        <input type="text" id="company" name="company" placeholder="e.g. Acme Inc." required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required>
      </div>

      <div class="form-group">
        <label for="password">Create Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="account-btn">Create Account</button>

      <p class="signin-prompt">
        Already have an account? <a href="?login">Sign in here</a>
      </p>

    </form>
  </div>
</section>
