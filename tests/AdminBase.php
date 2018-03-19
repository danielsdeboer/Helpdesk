<?php

namespace Aviator\Helpdesk\Tests;

abstract class AdminBase extends BKTestCase
{
    /**
     * Act as the supervisor.
     * @return void
     */
    protected function beSuper()
    {
        $this->be($this->make->super->user);
    }

    /**
     * Call the uri with the verb and optional request.
     * @param array $request
     * @return void
     */
    protected function callUri($request = [])
    {
        $this->call(static::VERB, static::URI, $request);
    }

    /**
     * Die and dump with the response content.
     * @return void
     */
    protected function ddc()
    {
        dd($this->response->content());
    }

    /**
     * This doesn't seem right.
     * @param $errors
     */
    protected function assertValidationFailed($errors)
    {
        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors($errors);
    }

    /**
     * Deny guests.
     * @return void
     */
    protected function noGuests()
    {
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /**
     * Deny users.
     * @return void
     */
    protected function noUsers()
    {
        $user = $this->make->user;

        $this->be($user);
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('403');
    }

    /**
     * Deny agents.
     * @return void
     */
    protected function noAgents()
    {
        $agent = $this->make->agent;

        $this->be($agent->user);
        $this->call(static::VERB, static::URI);

        $this->assertResponseStatus('403');
    }
}
