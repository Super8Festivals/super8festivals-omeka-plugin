import jQuery from "./vendor/jquery.js";

jQuery(document).ready(() => {
    jQuery("#file").change(function () {
        jQuery("#file + embed").remove();
        if (this.files && this.files[0]) {
            if (!isImage(this.files[0].name)) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                jQuery("#file + embed").remove();
                jQuery("#file").after("<embed src=\"" + e.target.result + "\" width=\"450\" height=\"300\" style=\"object-fit: cover;\">");
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function isImage(name) {
    return name.match(/.(jpg|jpeg|png|gif)$/i);
}