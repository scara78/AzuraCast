<?php
class C03_Station_RemoteRelaysCest extends CestAbstract
{
    /**
     * @before setupComplete
     * @before login
     */
    public function editRemoteRelays(FunctionalTester $I)
    {
        $I->wantTo('Create a station remote relay.');

        $station_id = $this->test_station->getId();
        $I->amOnPage('/station/'.$station_id.'/remotes');

        $I->see('Remote Relays');
        $I->click('add', '#content');

        $I->submitForm('.form', [
            'type' => 'shoutcast1',
            'url' => 'http://test.local',
            'display_name' => 'Test Relay'
        ]);

        $I->seeCurrentUrlEquals('/station/'.$station_id.'/remotes');

        $I->see('Test Relay');

        $I->click(\Codeception\Util\Locator::lastElement('.btn-danger'));

        $I->seeCurrentUrlEquals('/station/'.$station_id.'/remotes');

        $I->dontSee('Test Relay');
    }
}
