<?php

interface Service {
	public function getServerList();
	public function getChannelList($datatables = false);
	public function getChannelGlobalActivity($type, $datatables = false);
}