<?php

namespace App\Dto;

class ViewTask
{
    private String $name;
    private String $description;
    private String $status;
    private String $assigned;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * @param string $assigned
     */
    public function setAssigned($assigned): void
    {
        $this->assigned = $assigned;
    }


}