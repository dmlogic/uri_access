<?php namespace UriAccess;
/**
 * Checks the current URI against
 */

class Checker {

    private $map;

    private $mapParsed = false;

    private $checkGroups;

    private $checkUsers;

    //--------------------------------------------------------------------------

    public function __construct(array $uriMap)
    {
        $this->setMap($uriMap);
    }

    //--------------------------------------------------------------------------

    private function setMap($map)
    {
        $this->map = $map;
    }

    //--------------------------------------------------------------------------

    private function parseMap()
    {
        $map = [];
        foreach($this->map as $route => $rules) {

            if(!is_array($rules)) {
                $rules = [$rules];
            }

            if(empty($rules['group_ids'])) {
                $rules['group_ids'] = [];
            }
            if(empty($rules['user_ids'])) {
                $rules['user_ids'] = [];
            }

            $map[$route] = $rules;

        }

        $this->map = $map;
    }

    //--------------------------------------------------------------------------

    public function checkAllowed($uri,$groupIds = null,$userIds = null)
    {

        if(false === $this->mapParsed) {
            $this->parseMap();
        }

        $this->setCheckAttribute('groups',$groupIds);

        $this->setCheckAttribute('users',$userIds);

        return $this->checkUri($uri);
    }

    //--------------------------------------------------------------------------

    private function checkUri($uri)
    {
        $allowed = true;

        foreach ($this->map as $route => $rules) {

            try {
                preg_match($route, $uri,$match);
            } catch(\Exception $e) {
                continue;
            }

            if($match) {

                // Any match on this, we're allowed straight in
                if($this->compareIds($this->checkGroups,$rules['group_ids'])) {
                    return true;
                }

                if($this->compareIds($this->checkUsers,$rules['user_ids'])) {
                    return true;
                }

                // Don't give up if we have no match yet, another route may let us in
                $allowed = false;
            }
        }

        return $allowed;
    }

    //--------------------------------------------------------------------------

    private function compareIds($mine, $against)
    {
        return (array_intersect($mine, $against));
    }

    //--------------------------------------------------------------------------

    private function setCheckAttribute($attribute,$input)
    {
        $att = 'check'.ucfirst($attribute);

        if(!is_array($input)) {
            $input = [$input];
        }

        $this->$att = $input;
    }
}