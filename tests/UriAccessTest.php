<?php
class testUriAccess extends PHPUnit_Framework_TestCase
{
    private $map = [
        '/^protect\/me/' => [
            'group_ids' => [1,5,6],
            'user_ids'  => [5]
        ],
        '/ontheend$/' => [
            'group_ids' => [8],
            'user_ids'  => []
        ]
    ];

    //--------------------------------------------------------------------------

    public function testInstantiate()
    {
        $checker = new UriAccess\Checker([]);

        $this->assertInstanceOf('UriAccess\Checker', $checker);
    }

    //--------------------------------------------------------------------------

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMapNotArray()
    {
        $checker = new UriAccess\Checker('not an array');
    }

    //--------------------------------------------------------------------------

    public function testNonProtectedUri()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('not/protected',1);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------------

    public function testAllowedGroupProtectedUri()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('protect/me',1);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------------

    public function testDeniedGroupProtectedUri()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('protect/me',7);

        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------------

    public function testAllowedUserProtectedUri()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('protect/me',null,5);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------------

    public function testDeniedUserUri()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('protect/me',null,1);

        $this->assertFalse($result);
    }

    //--------------------------------------------------------------------------

    public function testAllowedUserUriLaterRoute()
    {
        $checker = new UriAccess\Checker($this->map);

        $result = $checker->checkAllowed('protect/me/ontheend',8);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------------

    public function testBadRegxReturnsTrue()
    {
        $map = $this->map;
        $map['badregx'] = [];

        $checker = new UriAccess\Checker($map);

        $result = $checker->checkAllowed('protect/me',1);

        $this->assertTrue($result);
    }

    //--------------------------------------------------------------------------

    public function testInvalidMapFormatReturnsTrue()
    {
        $checker = new UriAccess\Checker([1,2,3]);

        $result = $checker->checkAllowed('protect/me',1);

        $this->assertTrue($result);
    }
}