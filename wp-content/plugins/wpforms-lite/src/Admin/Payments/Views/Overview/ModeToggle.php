<?php

namespace WPForms\Admin\Payments\Views\Overview;

/**
 * Payments Overview Mode Toggle class.
 *
 * @since 1.8.2
 */
class ModeToggle {

	/**
	 * Determine if the toggle should be displayed and render it.
	 *
	 * @since 1.8.2
	 */
	public function display() {

		// Bail early if no payments are found in test mode.
		if ( ! $this->should_display() ) {
			return;
		}

		$this->render();
	}

	/**
	 * Look for at least one payment in test mode.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function should_display() {

		return wpforms()->get( 'payment' )->get_payments(
			[
				'mode'   => 'test',
				'number' => 1,
			]
		);
	}

	/**
	 * Display the toggle button.
	 *
	 * @since 1.8.2
	 */
	private function render() {

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/mode-toggle',
			[
				'mode' => Page::get_mode(),
			],
			true
		);
	}
}
