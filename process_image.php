<?php

$data = json_decode(file_get_contents('php://input'), true);
$imageData = $data['image'];

// Remove desnecessário como "data:image/png;base64,"
$imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
$imageData = base64_decode($imageData);

$googleApiKey = 'YOUR_GOOGLE_CLOUD_VISION_API_KEY';
$url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $googleApiKey;

$request = [
    'requests' => [
        [
            'image' => [
                'content' => base64_encode($imageData),
            ],
            'features' => [
                [
                    'type' => 'DOCUMENT_TEXT_DETECTION'
                ]
            ]
        ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($request),
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
    die('Erro ao acessar o serviço de OCR');
}

// Decode o resultado do Google Cloud Vision
$resultData = json_decode($result, true);
$detectedText = $resultData['responses'][0]['fullTextAnnotation']['text'];

// Valide e processe o texto reconhecido
echo json_encode(['text' => $detectedText]);

?>