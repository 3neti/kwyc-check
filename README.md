#KwYC Check

##Mobile App Description

Quickly register SIMs/IDs for accreditation and membership in
participating institutions. Help yourself, your family, friends,
neighbors and co-workers in certifying their credentials
using AI-based face authentication in 30 seconds!

nLITn KwYC lets you match the picture in accredited government-issued
identification with the card bearer's facial biometrics. Details such as
name, address and birthdate are automagically recognized and
then safely transmitted to the requesting institution, without
leaving any scintilla of data in the mobile device.

All KYC data captured in the process are encrypted before securely
transmitting to the official servers of the participating institutions.
Data access are governed by the National Privacy Commission.

Subscribers may become onboarding agents of participating institutions
and earn by using the platform as a source of supplemental income.
Agents may charge the institution or the registrant per transaction.
An electronic wallet with enough credits is extended for personal
and family onboarding. ☺

###Steps

1. Select from the list of accredited government agencies, then scan the ID.
2. Take a selfie of the applicant. Upload and proceed when it matches.
3. Enter mobile number. An SMS will be sent to the subscriber.
4. Enter the valid OTP as indicated in the SMS notification.
5. Acknowledge the privacy agreement and you're done!

###Features and Benefits

1. Face Authentication - fast, high-precision face to ID picture matching
2. OCR - fast, no manual data entry, no discretion in processing data
3. OTP - definitive mobile authentication, bank-grade, geo-tagging
4. Closed-loop wallet - Grab/Uber style top up to render service

###Participating Institutions

1. Smart, Globe Telecom, Dito Telecommunity via TCI
2. Philippine Charity Sweepstakes Office
3. Bureau of Immigration via DFA
4. Iglesia ni Cristo
5. PAGCOR
6. UAAP
7. LTO

##Tasks
1. Anybody can register an organization and becomes the default admin.
2. An organization can be paired with a domain name.
3. The domain name of the organization can be confirmed by the admin with the same domain name in email.
4. Admin can configure the organization campaigns.
5. Each campaign has repository.
6. Any user with enough credits can order a campaign.
7. A campaign has a repository and the service package required.
8. Each packages has itemized products.
##Enumeration
###Channel
i.e. Email, SMS, HTTP POST 
###Format
i.e. TXT, CSV, XLS, SQL 
##Entities
###Organization
1. Name e.g. Acme Corporation, Garnet PSHS SY 1983-1984
2. Admin [User]
###Product
1. Code
2. Name i.e. Face Check, OTP, OCR, Dedup
3. Price
###Package (Service Package)
1. Code
2. Name e.g. Onboarding, Onboarding w/ OTP, OTP
3. Price
###PackageProducts
1. Package
2. Product
###Repository
1. Name e.g. Project 1 Lead Generation
2. Organization
3. Channel (see enum)
4. Format (see enum)
5. Address e.g. somebody@domain.com
6. Command
###Campaign
1. Package
2. Repository
3. Active
4. Start Date
5. End Date
###Order
1. User
###OrderItem
1. Order
2. Campaign
3. Qty (default 1)
4. Price
