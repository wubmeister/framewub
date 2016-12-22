<?php

use PHPUnit\Framework\TestCase;

use Framewub\Rest\AbstractController;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class ACMockController extends AbstractController
{
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

class AbstractControllerTest extends TestCase
{
    public function testFindAll()
    {
        $controller = new ACMockController();
        ob_start();
        $response = $controller(new ServerRequest());
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '[{"name":"Lorem ipsum","description":"Lorem ipsum, doler sit amet"},{"name":"Foo bar","description":"This is just dome dummy data"}]',
            $response->getBody()->getMockContents()
        );
    }

    public function testFindOne()
    {
        $controller = new ACMockController();
        ob_start();
        $response = $controller(new ServerRequest([ 'id' => 1 ]));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"name":"Lorem ipsum","description":"Lorem ipsum, doler sit amet"}',
            $response->getBody()->getMockContents()
        );

        ob_start();
        $response = $controller(new ServerRequest([ 'id' => 2 ]));
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

        $controller = new ACMockController();
        $request = new ServerRequest();
        ob_start();
        $response = $controller($request->withMethod('POST'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"name":"Trens roxnas et plokeing","description":"Lucius in domus est"}',
            $response->getBody()->getMockContents()
        );
    }

    public function testUpdate()
    {
        $_POST['name'] = "Updated stuff";
        $_POST['description'] = "Lucius in domus est";

        $controller = new ACMockController();
        $request = new ServerRequest([ 'id' => 3 ]);
        ob_start();
        $response = $controller($request->withMethod('PUT'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"id":3,"name":"Updated stuff","description":"Lucius in domus est"}',
            $response->getBody()->getMockContents()
        );
    }

    public function testDelete()
    {
        $_POST['name'] = "Updated stuff";
        $_POST['description'] = "Lucius in domus est";

        $controller = new ACMockController();
        $request = new ServerRequest([ 'id' => 3 ]);
        ob_start();
        $response = $controller($request->withMethod('DELETE'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(
            '{"success":true,"message":"Deleted #3","id":3}',
            $response->getBody()->getMockContents()
        );
    }
}
