// Crear el elemento script
var script = document.createElement('script');

// Establecer los atributos
script.setAttribute('data-embed-id', 'b2f84427-3d47-4072-bf63-efd057936434');
script.setAttribute('data-base-api-url', 'http://localhost:3001/api/embed');
script.src = 'http://localhost:3001/embed/anythingllm-chat-widget.min.js';

// Añadir el script al documento
document.head.appendChild(script);
