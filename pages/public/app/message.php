
  
<?php
  // Set message based on the GET parameter
    if (isset($_GET["wrong-password"])) {
        $name = "Wrong Password";
        $description = "The password you entered is incorrect. Please try again or return to the previous page.";
    } elseif (isset($_GET["profile-created"])) {
        $name = "Profile Created";
        $description = "Your profile was created successfully. You can now log in to your account and enjoy our app.";
    } elseif (isset($_GET["user-not-found"])) {
        $name = "User Not Found";
        $description = "Your account doesn't exist. You might have mistyped your credentials or missed a letter.";
    } elseif (isset($_GET["event-updated"])) {
        $name = "Event Updated";
        $description = "Your event was successfully updated.";
    } elseif (isset($_GET["event-created"])) {
        $name = "Event Created";
        $description = "Your event was successfully created.";
    } elseif (isset($_GET["event-deleted"])) {
        $name = "Event Deleted";
        $description = "Your event was successfully deleted.";
    } elseif (isset($_GET["error"])) {
        $name = "Error";
        $description = "An unexpected error occurred. Please try again later.";
    } elseif (isset($_GET["missing-required"])) {
        $name = "Missing Required Fields";
        $description = "Please fill in all required fields and try again.";
    } else {
        $name = "Notice";
        $description = "Something happened. Please check and try again.";
    }
?>


<section id="dialog" class="flex items-center justify-center min-h-[70vh]">
  <main class="bg-gray-900 text-white max-w-xl w-full mx-auto p-10 rounded-xl shadow-lg border-4 border-gray-700">
    <div>
      <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        <span><?php echo $GLOBALS["name"]?></span>
      </h2>
      <p class="mb-6 leading-relaxed text-gray-300">
        <?php echo $GLOBALS["description"]?>
      </p>
      <a href="javascript:history.back()" class="block">
        <button
          class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200 border border-red-800"
          aria-label="Go back"
        >
          Go Back
        </button>
      </a>
    </div>
  </main>
</section>
