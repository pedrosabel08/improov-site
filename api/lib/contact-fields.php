<?php

declare(strict_types=1);

/** Map the short form to the existing contact record and notification queue. */
function normalize_contact_fields(array $data): array
{
    $digits = preg_replace('/\D/', '', $data['telefone']);
    if (!preg_match('/^\+?[0-9\s().-]+$/', $data['telefone']) || strlen($digits) < 8 || strlen($digits) > 15) {
        throw new InvalidArgumentException('Informe um WhatsApp válido, com DDD ou código do país.');
    }
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Informe um e-mail válido.');
    }
    $launches = ['ate_6_meses' => 'Até 6 meses', '6_a_12_meses' => '6 a 12 meses', 'mais_de_12_meses' => 'Mais de 12 meses', 'em_vendas' => 'Já em vendas'];
    if (!isset($launches[$data['prazo_lancamento']])) {
        throw new InvalidArgumentException('Selecione quando pretende lançar o empreendimento.');
    }
    if (!in_array($data['idioma'], ['pt-BR', 'en', 'es'], true)) {
        throw new InvalidArgumentException('Selecione um idioma válido.');
    }
    $data['tipo_interesse'] = 'Avaliação de lançamento';
    $data['empreendimento'] = '';
    $data['mensagem'] = 'Cargo: ' . $data['cargo'] . "\nPrevisão de lançamento: " . $launches[$data['prazo_lancamento']];
    return $data;
}
