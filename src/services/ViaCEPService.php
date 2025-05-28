<?php

class ViaCEPService {
    private $api_url = "https://viacep.com.br/ws/";

    public function getAddress($cep) {
        $cep = preg_replace('/[^0-9]/', '', $cep); // Remove caracteres não numéricos
        if (strlen($cep) != 8) {
            return ['error' => 'CEP inválido.'];
        }

        $url = $this->api_url . $cep . "/json/";
        $response = @file_get_contents($url); // Usar @ para suprimir warnings em caso de erro de rede

        if ($response === FALSE) {
            return ['error' => 'Erro ao consultar o ViaCEP.'];
        }

        $data = json_decode($response, true);

        if (isset($data['erro']) && $data['erro']) {
            return ['error' => 'CEP não encontrado.'];
        }

        return $data;
    }
}