<?php
interface DashboardPanelContentProvider {
	public function getPanelContent(DashboardPanel $panel);
}