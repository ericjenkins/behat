@javascript
Feature: Spine Center Directory
  In order to prove Doctor pages are working properly
  As an anonymous user
  I need to verify presence of content on published doctor pages.

  Scenario: DEV-628 Assert presence of content on Dr. Wallach's page
    Given I am at "doctor/orthopedic-surgeon/corey-wallach-alexandria-va"
    Then I should see "Meet Dr. Corey J. Wallach, MD" in the "div.practice-about > h2" element
    And I should see "Orthopedic Surgeon" in the "div.practice-particulars" element
    And I should see "Acute Pain" in the "div.practice-conditions" element
    And I should see "Laminoplasty" in the "div.practice-treatments" element
    #And I should see "I highly recommend Dr. Corey Wallach" in the "div.doctor-testimonials" element
    And I should see "Fellowship, University of California at Los Angeles" in the "div.doctor-accolades__distinctions" element
    And I should see "Board Certified, American Board of Orthopaedic Surgery" in the "div.doctor-accolades__certifications" element

  Scenario: DEV-628 Assert presence of content on BioSpine Institute page
    Given I am at "doctor/spine-center/biospine-institute"
    Then I should see "Learn About BioSpine Institute" in the "div.practice-about" element
    And I should see "Dr. Frank Bono" in the "div.physician-wrapper" element
    And I should see "Spine Center" in the "div.practice-particulars" element
    And I should see "Upper Back Pain" in the "div.practice-conditions" element
    And I should see "Spine Surgery" in the "div.practice-treatments" element
    And I should see "Tampa, FL 33607" in the "div.office_details" element
    And I should see "Call for My Appointment" in the "div.profile--bottom-call-to-action-buttons" element
    And I should see "Request My Appointment Online" in the "div.profile--bottom-call-to-action-buttons" element
