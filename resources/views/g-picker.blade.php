<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />

    <title>Google Picker Example</title>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

    <script type="text/javascript">
        // The Browser API key obtained from the Google API Console.
        // Replace with your own Browser API key, or your own key.
        var developerKey = 'cd16d868fa2179d792807a3a18439ed6440b0cd1';

        // The Client ID obtained from the Google API Console. Replace with your own Client ID.
        var clientId = "636595320434-iln7lnirphflalaqtni2j97v8j5l3i28.apps.googleusercontent.com"

        // Replace with your own project number from console.developers.google.com.
        // See "Project number" under "IAM & Admin" > "Settings"

        var key = 'AIzaSyB1tsrMiKH4-lydNlAsPHRHThpRPogaerk';
        var appId = '636595320434';

        // Scope to use to access user's Drive items.
        var scope = ['https://www.googleapis.com/auth/drive.file'];

        var pickerApiLoaded = false;
        var oauthToken;

        // Use the Google API Loader script to load the google.picker script.
        function loadPicker() {
            gapi.load('auth', {
                'callback': onAuthApiLoad
            });
            gapi.load('picker', {
                'callback': onPickerApiLoad
            });
        }

        function onAuthApiLoad() {
            window.gapi.auth.authorize({
                    'client_id': clientId,
                    'scope': scope,
                    'immediate': false
                },
                handleAuthResult);
        }

        function onPickerApiLoad() {
            pickerApiLoaded = true;
            createPicker();
        }

        function handleAuthResult(authResult) {
            if (authResult && !authResult.error) {
                oauthToken = authResult.access_token;
                createPicker();
            }
        }

        // Create and render a Picker object for searching images.
        function createPicker() {
            if (pickerApiLoaded && oauthToken) {
                var view = new google.picker.View(google.picker.ViewId.DOCS);
                // view.setMimeTypes("image/png,image/jpeg,image/jpg");
                var media_library_picker = new google.picker.PickerBuilder()
                    .enableFeature(google.picker.Feature.NAV_HIDDEN)
                    .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                    .setAppId(appId)
                    // .setTitle("Media library")
                    .hideTitleBar()
                    .setOAuthToken(oauthToken)
                    .addView(view)
                    // .addView(new google.picker.DocsUploadView())
                    .setOrigin(window.location.protocol + '//' + window.location.host)
                    .setDeveloperKey(key)
                    .setCallback(pickerCallback)
                    .toUri();

                // .build()
                // media_library_picker.setVisible(false);

                var upload_picker = new google.picker.PickerBuilder()
                    .enableFeature(google.picker.Feature.NAV_HIDDEN)
                    //.enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                    .setAppId(appId)
                    // .setTitle("Upload")
                    .hideTitleBar()
                    .setOAuthToken(oauthToken)
                    //.addView(view)
                    .addView(new google.picker.DocsUploadView())
                    .setDeveloperKey(key)
                    .setOrigin(window.location.protocol + '//' + window.location.host)
                    .setCallback(pickerCallback)
                    .toUri();


                var Gdrive_picker = new google.picker.PickerBuilder()
                    .enableFeature(google.picker.Feature.NAV_HIDDEN)
                    .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
                    .setAppId(appId)
                    .setTitle("Google Drive")
                    .setOAuthToken(oauthToken)
                    .addView(view)
                    // .addView(new google.picker.DocsUploadView())
                    .setDeveloperKey(key)
                    .setOrigin(window.location.protocol + '//' + window.location.host)
                    .setCallback(pickerCallback)
                    .toUri();

                var media_library_iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
                media_library_iframe.attr({
                    style: 'width: 100%; height: 100%',
                    src: media_library_picker
                });

                var upload_iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
                upload_iframe.attr({
                    style: 'width: 100%; height: 100%',
                    src: upload_picker
                });

                var Gdrive_picker_iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
                Gdrive_picker_iframe.attr({
                    style: 'width: 100%; height: 100%',
                    src: Gdrive_picker
                });

                $("#media_library_frame").append(media_library_iframe);
                $("#upload_frame").append(upload_iframe);
                $("#Gdrive_frame").append(Gdrive_picker_iframe);


            }
        }

        // width: 500,
        // height: 500,

        // A simple callback implementation.
        function pickerCallback(data) {
            alert("dasdas");
            console.log(data)
            if (data.action == google.picker.Action.PICKED) {
                var fileId = data.docs[0].id;
                alert('The user selected: ' + fileId);
            }
        }
    </script>
    <style>
        #wrapper {
            position: absolute;
            left: 100px;
            top: 100px;
            width: 1000px;
            height: 600px;
            border: 1px solid #00000010;
            background: white;
        }

        #panel_wrapper {
            width: 100%;
            height: 100%;
        }

        .mdl-tabs,
        #media_library_frame,
        #upload_frame,
        #Gdrive_frame {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <button onclick="showPickerDialog()">Show Picker Dialog</button>
    <div id="wrapper">
        <div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
            <div class="mdl-tabs__tab-bar">
                <a href="#media_library_frame" class="mdl-tabs__tab is-active">Media Library</a>
                <a href="#upload_frame" class="mdl-tabs__tab">Upload</a>
                <a href="#Gdrive_frame" class="mdl-tabs__tab">Google Drive</a>
            </div>



            <div id="panel_wrapper">
                <div class="mdl-tabs__panel is-active" id="media_library_frame">

                </div>
                <div class="mdl-tabs__panel" id="upload_frame">

                </div>
                <div class="mdl-tabs__panel" id="Gdrive_frame">

                </div>
            </div>
        </div>
    </div>
    <div id="result"></div>


    <!-- The Google API Loader script. -->
    <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
    <script>
        function showPickerDialog() {
            loadPicker()
        }
    </script>

</body>

</html>