<?php

interface Service {
    public function getCurrentStatus();
    public function getMaxValues();
    public function getUserCount($mode = null, $target = null);
    public function getClientStats($mode = null, $target = null);
    public function getCountryStats($mode = null, $target = null);
    public function makeCountryPieData($result, $sum);
    public function makeClientPieData($result, $sum);
    public function getUserHistory();
    public function getChannelHistory();
    public function getServerHistory();

    public function getServerList();
    public function getServer($server);
    public function getOperatorList();

    public function getChannelList($datatables = false);
    public function getChannelBiggest($limit = 10);
    public function getChannelTop($limit = 10);
    public function getChannel($chan);
    public function getChannelUsers($chan);
    public function getChannelGlobalActivity($type, $datatables = false);
    public function getChannelActivity($chan, $type, $datatables = false);
    public function getChannelHourlyActivity($chan, $type);
    public function checkChannel($chan);
    public function checkChannelStats($chan);

    public function getUsersTop($limit = 10);
    public function getUser($mode, $user);
    public function getUserChannels($mode, $user);
    public function getUserGlobalActivity($type, $datatables = false);
    public function getUserActivity($mode, $user, $chan);
    public function getUserHourlyActivity($mode, $user, $chan, $type);
    public function checkUser($user, $mode);
    public function checkUserStats($user, $mode);
}
