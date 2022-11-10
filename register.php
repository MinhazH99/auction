<?php 

include_once("header.php")?>

<script defer src='js/form.js'></script>
<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create auction form -->
<form id="form" method="POST" action="process_registration.php">
  <!-- <div class="form-group row">
    <label for="accountType" class="col-sm-2 col-form-label text-right">Registering as a:</label>
	<div class="col-sm-10">
	  <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" checked>
        <label class="form-check-label" for="accountBuyer">Buyer</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller">
        <label class="form-check-label" for="accountSeller">Seller</label>
      </div>
      <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div> -->
  <div class="form-group row">
    <label for="firstName" class="col-sm-2 col-form-label text-right">Name</label>
	<div class="col-sm-10">
      <input required type="text" class="form-control" name="firstName" id="firstName" placeholder="Name">
      <small id="firstNameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>
  <div class="form-group row">
    <label for="lastName" class="col-sm-2 col-form-label text-right">Surname</label>
	<div class="col-sm-10">
      <input required type="text" class="form-control" name="lastName" id="lastName" placeholder="Surname">
      <small id="lastNameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>
  <div class="form-group row">
    <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
	<div class="col-sm-10">
      <input required type="email" class="form-control" name="email" id="regEmail" placeholder="Email">
      <small id="regEmailHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  </div>
  <div class="form-group row">
    <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input required type="password" class="form-control" name="password" id="regPassword" placeholder="Password">
      <small id="regPasswordHelp" class="form-text text-muted"><span class="text-danger">* Required.  Must be minimum 8 characters. Must contain letters <b>and</b> numbers.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
    <div class="col-sm-10">
      <input required type="password" class="form-control" id="regPasswordConfirmation" placeholder="Enter password again">
      <small id="regPasswordConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control">Register</button>
  </div>
</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>

</div>

<?php include_once("footer.php")?>