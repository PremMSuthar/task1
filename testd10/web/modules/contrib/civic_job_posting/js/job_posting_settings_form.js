(function ($) {

  Drupal.behaviors.jobPostingSettings = {
    attach: function (context, settings) {

      $(document, context).once('jobPostingSettings').on('change', 'input[name="files[jsonFile]"]', function(event) {

        var reader = new FileReader();

        reader.onload = function(event) {
                try {
                    var jsonObj = JSON.parse(event.target.result);
                    console.log(jsonObj.type);
                    if(jsonObj.type && jsonObj.project_id && jsonObj.private_key_id && jsonObj.private_key_id && jsonObj.private_key && jsonObj.client_email && jsonObj.client_id && jsonObj.auth_uri && jsonObj.token_uri && jsonObj.auth_provider_x509_cert_url && jsonObj.client_x509_cert_url){

                        $('#edit-type').val(jsonObj.type);
                        $('#edit-projectid').val(jsonObj.project_id);
                        $('#edit-privatekeyid').val(jsonObj.private_key_id);
                        $('#edit-privatekey').val(jsonObj.private_key);
                        $('#edit-clientemail').val(jsonObj.client_email);
                        $('#edit-clientid').val(jsonObj.client_id);
                        $('#edit-authuri').val(jsonObj.auth_uri);
                        $('#edit-tokenuri').val(jsonObj.token_uri);
                        $('#edit-authproviderx509certurl').val(jsonObj.auth_provider_x509_cert_url);
                        $('#edit-clientx509certurl').val(jsonObj.client_x509_cert_url);

                        alert('Your file have been imported. Please check  the values and then Save your Settings !');

                    } else {
                        alert('This .json file is not valid for google service account. Please try with the correct .json file or insert your values manually !');
                    }
                }
                catch(err) {
                    alert('This file is invalid. Please Upload a .json file !');
                }
        };

        reader.readAsText(event.target.files[0]);
      });
  }
};

})(jQuery);