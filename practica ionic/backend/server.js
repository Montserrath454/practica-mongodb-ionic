const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const PORT = 3000;

app.use(cors());
app.use(bodyParser.json());

app.post('/personas', (req, res) => {
    const nuevaPersona = req.body;
    
    console.log('-----------------------------------');
    console.log('📩 ¡DATOS RECIBIDOS DESDE IONIC!');
    console.log('Nombre:', nuevaPersona.nombre);
    console.log('Apellido:', nuevaPersona.apellido);
    console.log('Edad:', nuevaPersona.edad);
    console.log('Correo:', nuevaPersona.correo);
    console.log('-----------------------------------');

    res.status(201).json({
        mensaje: '✅ Usuario guardado exitosamente (Simulado)',
        data: nuevaPersona
    });
});

app.get('/', (req, res) => {
    res.send('Servidor corriendo localmente y listo para recibir datos de Ionic.');
});

app.listen(PORT, () => {
    console.log(`\n SERVIDOR LISTO EN: http://localhost:${PORT}`);
    console.log(`Nota: El guardado es simulado para evitar errores de IP.\n`);
});