<i>It is HelloSign callback script for insert documents fields into HubSpot.</i>

<h2>Setup to insert contacts</h2>
- Copy settings.example.php into settings.php
- Login into Hubspot.
- Open Profile & Preferences / Integratins / API Key. Copy and paste into settings.php
- Login into HelloSign.
- Open Integratins / API. Set script URI in "ACCOUNT CALLBACK" field.
For example https://example.com/hellosign-contacts.php

<h2>Setup to insert forms</h2>
- Copy settings.example.php into settings.php
- On Hubspot create new form with all fields from Hellosign document.
- On Hellosign edit document and paste fields identificators from Hubspot form.
- For text fields Hubspot form field must match exactly to Hellosign fields. For example: firstname - <b>firstname</b>.
- For checkbox fields Hellosign field name must be hubspot_field:hubspot_value. For example: checkbox married of marital_status must be - <b>marital_status:married</b>.
- When Hubspot form created copy <b>portalId</b> and <b>formId</b> from share code and paste into settings.php.
- Open Integratins / API. Set script URI in "ACCOUNT CALLBACK" field.
For example https://example.com/hellosign.php