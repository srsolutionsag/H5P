/**
 * This file contains some modifications for ILIAS which makes some functionality
 * compatible with H5P contents.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */

var il = il || {};
il.Accordion = il.Accordion || {};

(function (Accordion) {
  const handleAccordionClick = il.Accordion.clickHandler || function () {};

  /**
   * This function overrides the default event handler for accordion clicks. This
   * needs to be done, because H5P content rendered inside of a hidden or collapsed
   * accordion will not be able to calculate its dimensions correctly. Therefore,
   * we manually fire a resize event which H5P listens to, to recalculate the dimensions
   * after the accordion has been opened.
   *
   * @param {MouseEvent} event
   */
  Accordion.clickHandler = function (event) {
    handleAccordionClick(event);
    window.dispatchEvent(new Event('resize'));
  };
})(il.Accordion);
