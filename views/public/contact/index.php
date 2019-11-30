<?php
$head = array(
    'title' => "Contact",
);
echo head($head);
?>

<?php echo flash(); ?>


<?php
$name = (isset($_POST['name']) && !empty($_POST['name'])) ? $_POST['name'] : null;
$email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : null;
$subject = (isset($_POST['subject']) && !empty($_POST['subject'])) ? $_POST['subject'] : null;
$message = (isset($_POST['message']) && !empty($_POST['message'])) ? $_POST['message'] : null;
?>

<!--Section: Contact v.2-->
<section class="container">

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="row">
            <div class="col text-center">
                <div class="alert alert-primary" role="alert">
                    Thank you for your email, <?= $name; ?>!<br/>
                    We will email you back at <a href="mailto:<?= $email; ?>" class="alert-link"><?= $email; ?></a> as soon as we can.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col text-center">
            <h2>Contact Us</h2>
        </div>
    </div>

    <div class="row">
        <div class="col text-center">
            <p>Please do not hesitate to reach out to us at any time! We would love to hear from you.</p>
            <p>If you are looking to submit content for the website, please use <a href="submit">this link</a> instead.</p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="well well-sm">
                <form action="contact" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name (Personal/Organization)</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter name" required="required" name="name"/>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" placeholder="Enter email" required="required" name="email"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="subject" placeholder="Enter subject" required="required" name="subject"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required" placeholder="Message" name="message"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary pull-right" id="btnContactUs">
                                Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>


<?php echo foot(); ?>
