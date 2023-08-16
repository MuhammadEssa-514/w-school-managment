<?php

namespace WPForms\SmartTags\SmartTag;

/**
 * Class UserIp.
 *
 * @since 1.6.7
 */
class UserIp extends SmartTag {

	/**
	 * Get smart tag value.
	 *
	 * @since 1.6.7
	 * @since 1.8.2 Return empty string if IP collection is disabled. Return entry IP address if entry ID is provided.
	 *
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	public function get_value( $form_data, $fields = [], $entry_id = '' ) {

		if ( ! wpforms_is_collecting_ip_allowed() ) {
			return '';
		}

		if ( ! $entry_id ) {
			return esc_html( wpforms_get_ip() );
		}

		return wpforms()->get( 'entry' )->get( $entry_id )->ip_address;
	}
}
