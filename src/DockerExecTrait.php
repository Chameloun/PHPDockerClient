<?php

namespace Chameloun\PHPDockerClient;

use Chameloun\PHPDockerClient\DockerConfig\HttpMethodEnum;
use Chameloun\PHPDockerClient\DockerError\DockerErrorCodeEnum;
use Chameloun\PHPDockerClient\DockerError\DockerException;

trait DockerExecTrait
{
    public function execCreate(string $id_name, bool $AttachStdin = false, bool $AttachStdout = false, bool $AttachStderr = false, array $Cmd = [], string $WorkingDir = "") : string {

        try {

            $data = json_encode(array(
                "AttachStdin" => $AttachStdin,
                "AttachStdout" => $AttachStdout,
                "AttachStderr" => $AttachStderr,
                "Cmd" => $Cmd,
                "WorkingDir" => $WorkingDir
            ), JSON_THROW_ON_ERROR);

            return $this->dockerApiRequest(HttpMethodEnum::POST, '/containers/'.$id_name.'/exec', $data, allowed_codes: array(201))->Id;

        } catch (DockerException $e) {

            throw new DockerException($e->getMessage(), DockerErrorCodeEnum::from($e->getCode()));

        }

    }
    public function execStart(string $exec_id, string $data) : void {

        $this->dockerApiRequest(HttpMethodEnum::POST, '/exec/'.$exec_id.'/start', $data, allowed_codes: array(200));

    }
}