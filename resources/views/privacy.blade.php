<x-guest-layout>
    <div class="relative">
        <nav class="sticky top-0 z-30 bg-white shadow-md">
            <!-- Primary Navigation Menu -->
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 sm:h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex items-center flex-shrink-0">
                            <a href="/">
                                <img class="w-52" src="{{ asset('images/splashr-logo.png') }}" alt="Splashr">
                            </a>
                        </div>
                    </div>
                    <!-- right nav items -->
                    @if (Route::has('login'))
                    <div class="flex items-center space-x-5">
                        @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-1.5 transition text-xl text-white bg-indigo-600 rounded-full hover:bg-indigo-800"> Dashboard </a>
                        @else

                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-6 py-1.5 transition text-xl text-white bg-indigo-600 rounded-full hover:bg-indigo-800"> Register </a>
                        @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </nav>

        <div class="max-w-screen-lg mx-auto">
            <div class="py-10 md:pt-20 md:pb-32">
                <h1 class="text-3xl text-center font-bold">
                    PRIVACY POLICY
                </h1>
                <h3 class="mt-10 text-xl font-bold">General Information</h3>
                <p class="mt-5 text-md">
                    This Privacy Policy outlines Splashr SAS and its affiliates (“Splashr”, “we” or “us”) practices with respect to collecting, using and disclosing information when providing our online services (called the “Services”).<br />This is separate to our Terms, which sets out more information about the Services (“Terms” or “Agreement”).
<br /><br />
Splashr platform allows advertisers to create videos dedicated to Social media. Based on the assets that the client is uploading on the platform, Splashr create videos animated.

                </p>
                <h3 class="mt-10 text-xl font-bold">Users Information</h3>
                <p class="mt-5 text-md">
                    By creating an account, users may provide personal information such as : Email Address, Name, Phone. Splashr does not collect your payment information directly.<br /><br />

Users may provide information by contacting us via our website, support channels, creating an account, or using our Services. This information includes user activity, location, name, email address, phone number, mailing address, and any other information you provide us voluntarily.
                </p>

                <h3 class="mt-10 text-xl font-bold">Use of Information Collected</h3>
                <p class="mt-5 text-md">
                We collect and process User Data in order to be sure of the identity of the client, and in order to :
                <ul class="list-inside list-disc">
                    <li>
                    To provide our Services, products, content, and functionality;</li>
                    <li>To communicate with you regarding your purchase, inquiries, support request, feedback, or questions;</li>
                    <li>To register, maintain and manage your user account or membership with us; and</li>
                    <li>to administer and provide Services and customer support per your request.</li>
                </ul>
                </p>

                <h3 class="mt-10 text-xl font-bold">Sharing of information</h3>
                <p class="mt-5 text-md">
                We may share information with third parties in the ways and for the purposes described above.<br /><br />
                We may share and disclose such your information, if we believe in good faith that such disclosure is necessary or required: 
                    <ol class="ml-20 max-w-prose" list-inside >
                        <li>
                            (i) to comply with a law, regulation, governmental or securities exchange requirement, court order, judicial proceeding, or legal process, such as a subpoena or a search warrant; 
                        </li>
                        <li>
                            (ii) to address a violation of the law; 
                        </li>
                        <li>
                            (iii) to investigate fraud or criminal activity, and to protect our rights or those of our affiliates, vendors and users, or as part of legal proceedings affecting or may affect us or our affiliates, vendors or users.
                        </li>
                    </ol>
                </p>

                <h3 class="mt-10 text-xl font-bold">Visitor Information</h3>
                <p class="mt-5 text-md">
                When you visit our website and use our Services, we automatically collect information sent to us by your computer, or other access devices. This information includes: (i) device information (e.g. the type of browser and operating system your device uses, language preference, domain name, access time); (ii) your IP address; and (iii) alerts for troubleshooting errors and bugs. Where you are not logged into your account, this information is unidentified to you and we are not aware of the identity of the user from which this information is collected.
                </p>

                <h3 class="mt-10 text-xl font-bold">Cookies</h3>
                <p class="mt-5 text-md">
                    A cookie is a small data file that is sent to your device when you first visit a website. Cookies usually include an identification number that is unique to the device you are using. Such identifiers can help us better understand our users and how they are using the Site and the Services.
                    <br /><br />
                    Cookies also enable the recognition of a user when they re-visit the Site, keeping their settings and preferences and ability to offer customized features. If you prefer, however, at any time you can reset your browser, so it refuses all cookies or notifies you when a cookie is being sent.
                    <br /><br />
                    The Services may implement the following types of cookies: (i) cookies implemented by us for the purposes described above (“First Party Cookie”); and (ii) third-party cookies which are set by other online services who run content on the page you are viewing, for example by third party analytics companies who monitor and analyze our web access such as Google Analytics, Facebook Analytics that is used to track statistics and user demographics, interests, and behavior on websites and apps. 
                    <br /><br />
                    You may remove the cookies by following the instructions of your device preferences; however, if you choose to disable cookies, some features of our Services may not operate properly, and your online experience may be limited. You may also configure your browser settings to use the Services without some cookie functionality. You can delete cookies manually or set your browser to automatically delete cookies on a predetermined schedule.
                </p>

                <h3 class="mt-10 text-xl font-bold">Policy Regarding Children</h3>
                <p class="mt-5 text-md">
                    The Site and services of Splashr are not targeted to, nor intended for children under the age of 18. Nevertheless, you must be 13 years old or older to use the Site and the Services; we do not knowingly collect information from children under the age of 13. If we become aware that we have collected Personal Information from a child under the age of 13 years old, we will use commercially reasonable efforts to delete such information from our database. If you are between 13 and 17 years old, you agree that you received your parent or legal guardian's consent to use and access the Site and the Services.
                </p>

                <h3 class="mt-10 text-xl font-bold">Protection of Information</h3>
                <p class="mt-5 text-md">
                    Information collected about you is protected in several ways. If you create an account, access by you to your Information is available through a password and unique user ID selected by you. Your password is encrypted. We recommend that you do not divulge your password to anyone. In addition, your personal information resides on proprietary secured servers that only selected personnel and contracts have access to via password.
                    <br /><br />
                    We use industry standard security measures to protect your Personal Information so that it is not made available to unauthorized parties. However, we cannot guarantee that the Personal Information submitted to, maintained on, or transmitted from our systems will be completely secure. If you have any questions regarding which measures and techniques we use, feel free to contact us at support@splashr.io
                </p>

                <h3 class="mt-10 text-xl font-bold">European Citizens</h3>
                <p class="mt-5 text-md">
                    If you are a European resident, the following disclosures are made pursuant to the EU General Data Protection Regulations:
                </p>
                <h5 class="ml-10 mt-5 text-sm font-bold">DATA CONTROLLER</h5>
                <p class="ml-10 mt-5 text-md">
                    We are the data controller of the personal data we collect via the Service. Splashr SAS is the data controller of the personal data we collect via the Services and use and share as further described above. We are located at 5 avenue Foch, 75116 Paris.

                </p>

                <h5 class="ml-10 mt-5 text-sm font-bold">YOUR EU RIGHTS</h5>
                <p class="ml-10 mt-5 text-md">
                    You have the right to access the personal information about you that we store on our systems, and have us update, correct or delete it. In addition, you may at any time withdraw your consent to any activity which you provided us your consent. You are also entitled to obtain from us your Information (excluding data we obtained from other sources) in a structured, commonly used and machine-readable format, and have the right to transmit those data to another data controller. If you wish to exercise any of these rights, contact us at: support@splashr.io .<br /><br />
                    You can also have the right to lodge a complaint to the supervisory authority under the General data Protection Regulations ("GDPR"), in particular in the Member State of your residence, place of work or where the alleged infringement of the GDPR occured. 
                </p>

                <h5 class="ml-10 mt-5 text-sm font-bold">DATA RETENTION</h5>
                <p class="ml-10 mt-5 text-md">
                    We retain personal data as long as we need it for the purposes for which it was obtained or until you ask to delete it. If you ask us to delete the Information, we may still have to retain it to comply with our legal obligations, resolve disputes and enforce our agreements.
                </p>

                <h5 class="ml-10 mt-5 text-sm font-bold">LEGAL BASIS FOR PROCESSING PERSONAL DATA:</h5>
                <p class="ml-10 mt-5 text-md">
                    <table class="ml-10 table-fixed border border-collapse text-left ">
                        <tbody>
                            <tr>
                                <th class="py-3 px-5 border text-left w-1/3">
                                    Information Type
                                </th>
                                <th class="py-3 px-5 border text-left w-1/3">
                                    Purpose
                                </th>
                                <th class="py-3 px-5 border text-left w-1/3">
                                    Legal Basis
                                </th>
                            </tr>
                            <tr>
                                <td class="py-3 px-5 border">
                                    Processing Personal
                                </td>
                                <td class="py-3 px-5 border">
                                    Online marketing and provision of the services
                                </td>
                                <td class="py-3 px-5 border">
                                    The legal basis under EU law for processing and collecting your Personal Information is our legitimate interests in operating our Services.
                                </td>
                            <tr>
                            <tr>
                                <td class="py-3 px-5 border">
                                    Processing Personal
                                </td>
                                <td class="py-3 px-5 border">
                                    Profiled advertising
                                </td>
                                <td class="py-3 px-5 border">
                                    The legal basis under EU law for collecting and processing your Personal Information for profiled advertising purposes is your explicit consent.
                                </td>
                            <tr>
                            <tr>
                                <td class="py-3 px-5 border">
                                    Processing Analytical
                                </td>
                                <td class="py-3 px-5 border">
                                    Operation and enhancement
                                </td>
                                <td class="py-3 px-5 border">
                                    The legal basis under EU law for processing and collecting Analytical Data is our legitimate interests in operating our Apps, ongoing management of our business and business development.
                                </td>
                            <tr>
                        </tbody>
                    </table>
                </p>
                <h3 class="mt-10 text-lg font-bold">Questions?</h3>
                <p class="mt-5 text-md">
                    You may direct questions concerning this Privacy Policy by email to support@splashr.io by adding the word 'privacy' in the subject line.
                </p>

            </div>
        </div>
    </div>
</x-guest-layout>