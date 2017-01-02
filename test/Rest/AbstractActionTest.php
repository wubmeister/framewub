<?php

use PHPUnit\Framework\TestCase;

use Framewub\Rest\AbstractAction;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class ACMockAction extends AbstractAction
{
    public $didCheckAuth = false;
    public $mockAuthorize = true;

    protected function isAuthorized($action)
    {
        $this->didCheckAuth = $action;
        return $this->mockAuthorize;
    }

    protected function findAll()
    {
        return [
            [
                'name' => 'Lorem ipsum',
                'description' => 'Lorem ipsum, doler sit amet'
            ],
            [
                'name' => 'Foo bar',
                'description' => 'This is just dome dummy data'
            ]
        ];
    }

    protected function findById($id)
    {
        switch ($id) {
            case 1:
                return [
                    'name' => 'Lorem ipsum',
                    'description' => 'Lorem ipsum, doler sit amet'
                ];
                break;

            case 2:
                return [
                    'name' => 'Foo bar',
                    'description' => 'This is just dome dummy data'
                ];
                break;
        }
    }

    protected function add($values)
    {
        return $values;
    }

    protected function update($id, $values)
    {
        return array_merge([ 'id' => $id ], $values);
    }

    protected function delete($id)
    {
        return [ 'success' => true, 'message' => 'Deleted #'.$id, 'id' => $id ];
    }
}

class AbstractActionTest extends TestCase
{
    public function testFindAll()
    {
        $action = new ACMockAction();
        ob_start();
        $response = $action(new ServerRequest());
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '[{"name":"Lorem ipsum","description":"Lorem ipsum, doler sit amet"},{"name":"Foo bar","description":"This is just dome dummy data"}]',
            $response->getBody()->getMockContents()
        );
        $this->assertEquals(AbstractAction::FIND_ALL, $action->didCheckAuth);
    }

    public function testFindOne()
    {
        $action = new ACMockAction();
        ob_start();
        $response = $action(new ServerRequest([ 'id' => 1 ]));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"name":"Lorem ipsum","description":"Lorem ipsum, doler sit amet"}',
            $response->getBody()->getMockContents()
        );
        $this->assertEquals(AbstractAction::FIND_BY_ID, $action->didCheckAuth);

        ob_start();
        $response = $action(new ServerRequest([ 'id' => 2 ]));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"name":"Foo bar","description":"This is just dome dummy data"}',
            $response->getBody()->getMockContents()
        );
    }

    public function testInsert()
    {
        $_POST['name'] = "Trens roxnas et plokeing";
        $_POST['description'] = "Lucius in domus est";

        $action = new ACMockAction();
        $request = new ServerRequest();
        ob_start();
        $response = $action($request->withMethod('POST'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"name":"Trens roxnas et plokeing","description":"Lucius in domus est"}',
            $response->getBody()->getMockContents()
        );
        $this->assertEquals(AbstractAction::ADD, $action->didCheckAuth);
    }

    public function testUpdate()
    {
        $_POST['name'] = "Updated stuff";
        $_POST['description'] = "Lucius in domus est";

        $action = new ACMockAction();
        $request = new ServerRequest([ 'id' => 3 ]);
        ob_start();
        $response = $action($request->withMethod('PUT'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"id":3,"name":"Updated stuff","description":"Lucius in domus est"}',
            $response->getBody()->getMockContents()
        );
        $this->assertEquals(AbstractAction::UPDATE, $action->didCheckAuth);
    }

    public function testDelete()
    {
        $_POST['name'] = "Updated stuff";
        $_POST['description'] = "Lucius in domus est";

        $action = new ACMockAction();
        $request = new ServerRequest([ 'id' => 3 ]);
        ob_start();
        $response = $action($request->withMethod('DELETE'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"success":true,"message":"Deleted #3","id":3}',
            $response->getBody()->getMockContents()
        );
        $this->assertEquals(AbstractAction::DELETE, $action->didCheckAuth);
    }

    public function testUnauthorizedRequests()
    {
        $request = new ServerRequest();
        $idRequest = $request->withAttribute('id', 3);
        $action = new ACMockAction();
        $action->mockAuthorize = false;

        // Find all
        ob_start();
        $response = $action($request);
        ob_end_clean();
        $this->assertEquals('{"success":false}', $response->getBody()->getMockContents());
        $this->assertEquals(403, $response->getStatusCode());

        // Find by id
        ob_start();
        $response = $action($idRequest);
        ob_end_clean();
        $this->assertEquals('{"success":false}', $response->getBody()->getMockContents());
        $this->assertEquals(403, $response->getStatusCode());

        // Insert
        ob_start();
        $response = $action($request->withMethod('POST'));
        ob_end_clean();
        $this->assertEquals('{"success":false}', $response->getBody()->getMockContents());
        $this->assertEquals(403, $response->getStatusCode());

        // Update
        ob_start();
        $response = $action($idRequest->withMethod('PUT'));
        ob_end_clean();
        $this->assertEquals('{"success":false}', $response->getBody()->getMockContents());
        $this->assertEquals(403, $response->getStatusCode());

        // Delete
        ob_start();
        $response = $action($idRequest->withMethod('DELETE'));
        ob_end_clean();
        $this->assertEquals('{"success":false}', $response->getBody()->getMockContents());
        $this->assertEquals(403, $response->getStatusCode());
    }
}
