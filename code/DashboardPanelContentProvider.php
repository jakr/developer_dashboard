<?php
/**
 * Interface to provide a function to update the panel content.
 */
interface DashboardPanelContentProvider {
	/**
	 * An update callback function that is called before the dashboard
	 *  is displayed. The panel $panel will be overwritten by the return value
	 *  of this function. This function can be used just like getCMSFields().
	 * 
	 * @param DashboardPanel $panel
	 * @return DashboardPanel
	 */
	public function getPanelContent(DashboardPanel $panel);
}