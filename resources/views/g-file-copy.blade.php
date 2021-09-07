<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <title>Google File Copy</title>
</head>

<body>
    <div>
        <button onclick="authenticate().then(loadClient)">authorize and load</button>
        <button onclick="execute()">execute</button>
        <button onclick="copy()">Copy</button>
    </div>

    <!-- The Google API Loader script. -->
    <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        /**
         * Sample JavaScript code for drive.permissions.create
         * See instructions for running APIs Explorer code samples locally:
         * https://developers.google.com/explorer-help/guides/code_samples#javascript
         */

        // const oldFolderId = '1BcJx_rkfT2stE4RqIzkMfHxpx8RI-MB4' // didier 
        const oldFolderId = '1lBmR27TDEHcIQY9gzte2fORGHZGLszCx' // shamim 
        const newFolderId = '1Iz9nZSwJjb2YvnQDR_XIcYTV11IAZQJZ' // app
        // const fileId = '1FkIlFrgieabSswL7DlP68duPRBY5r4ME' // didier
        const fileId = '1LtiOAnthl6hgZE56RILhBM5c2RGjV4uw' // shamim

        function authenticate() {
            return gapi.auth2.getAuthInstance()
                .signIn({
                    scope: "https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file"
                })
                .then(function(response) {
                        console.log("Sign-in successful", response);

                        // add permission to the destination folder for this user
                        axios.post('google/drive/permission/create', {
                            fileId: newFolderId,
                            email: response.Qs.zt
                        })
                    },
                    function(err) {
                        console.error("Error signing in", err);
                    });
        }

        function loadClient() {
            gapi.client.setApiKey("AIzaSyB1tsrMiKH4-lydNlAsPHRHThpRPogaerk");
            return gapi.client.load("https://content.googleapis.com/discovery/v1/apis/drive/v3/rest")
                .then(function() {
                        console.log("GAPI client loaded for API");
                    },
                    function(err) {
                        console.error("Error loading GAPI client for API", err);
                    });
        }
        // Make sure the client is loaded and sign-in is complete before calling this method.
        function execute() {
            return gapi.client.drive.permissions.create({
                    fileId: fileId,
                    resource: {
                        role: "writer",
                        type: "user",
                        // emailAddress: "app@splashr.io"
                        emailAddress: "splashr-app-service-acct@splashr-app-306410.iam.gserviceaccount.com"
                    },
                    supportsAllDrives: true,
                    sendNotificationEmail: false
                })
                .then(function(response) {
                        // Handle the results here (response.result has the parsed body).
                        console.log("Response", response);
                    },
                    function(err) {
                        console.error("Execute error", err);
                    });
        }
        // copy
        async function copy() {
            // Create Copy of File
            const cloned = (await gapi.client.drive.files.copy({
                fileId: fileId,
                supportsAllDrives: true
            })).result

            console.log('cloned file response: ', cloned)

            // Move copy to new folder
            const response = await gapi.client.drive.files.update({
                fileId: cloned.id,
                addParents: newFolderId,
                removeParents: oldFolderId,
                resource: {
                    name: 'some name here'
                },
                fields: 'id, parents',
                supportsAllDrives: true
            })
            console.log('update response: ', response)
        }
        gapi.load("client:auth2", function() {
            gapi.auth2.init({
                client_id: "636595320434-iln7lnirphflalaqtni2j97v8j5l3i28.apps.googleusercontent.com"
            });
        });
    </script>
</body>

</html>