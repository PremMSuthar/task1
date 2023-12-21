INTRODUCTION
------------

With google Job Posting you can improve the job seeking experience
by adding job posting structured data to your job posting web pages.
Adding structured data makes your job postings eligible to appear
in a special user experience in Google Search results.

REQUIREMENTS
------------

This module requires paragraphs and field_group modules.
Ensure that Googlebot can crawl your job posting web pages
(not protected by a robots.txt file or robots meta tag).
Ensure that your host load settings allow for frequent crawls.
To use the Google Indexing API:

 * complete the prerequisites by enabling the Indexing API,
 * create a new service account,
 * verify ownership in Search Console,
 * get an access token to authenticate your API call.

INSTALLATION
-------

Installing the Job Posting module is simple:

1) Copy the civic_job_posting folder to the modules folder in
   your installation or run "composer require drupal/civic_job_posting"

2) Enable the module using Administer -> Extend page (/admin/modules)

CONFIGURATION
-------

 * To complete google verification and indexing go to Configuration
-> Web Services -> Job Posting Settings
(admin/config/services/jobpostingtsettings)
 * To administrate jobs go to Admin -> Content -> Jobs
(/admin/job)
 * To amend the views provided from the module go to Structure -> Views
-> Job View (/admin/structure/views/view/job_view)

CHANGELOG
---------
= 1.0.1 =
* Launch

= 1.0.2 =
* Use dependency injection in JobPostingUtils class

= 1.0.3 =
* Fix for https://www.drupal.org/project/civic_job_posting/issues/3226301

= 1.0.4 =
* Drupal 10 compatibility.

= 1.0.5 =
* Fix composer.json
