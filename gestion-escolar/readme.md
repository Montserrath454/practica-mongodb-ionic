# Gestión Escolar Backend

Backend del proyecto de gestión escolar usando PHP, Apache, MySQL y Docker.

## Levantar el proyecto

```bash
docker compose up --build
```

## URLs

Backend:

`http://localhost:8080`

phpMyAdmin:

`http://localhost:8081`

## Base de datos

- Usuario MySQL: `root`
- Contraseña: `root`
- Base de datos: `escuela`

## Endpoints

### 1. Listar alumnos

**Método:** GET

```text
/alumnos
```

Obtiene la lista completa de alumnos registrados.

---

### 2. Obtener alumno por ID

**Método:** GET

```text
/alumnos/{id}
```

Ejemplo:

```text
/alumnos/1
```

Obtiene los datos de un alumno específico mediante su ID.

---

### 3. Registrar alumno

**Método:** POST

```text
/alumnos
```

Body JSON:

```json
{
  "nombre": "Maria Gomez",
  "matricula": "2026001",
  "carrera": "Ingeniería en Sistemas"
}
```

---

### 4. Listar materias

**Método:** GET

```text
/materias
```

Obtiene la lista completa de materias.

---

### 5. Asignar o actualizar calificación

**Método:** PUT

```text
/calificaciones
```

Body JSON:

```json
{
  "alumno_id": 1,
  "materia_id": 2,
  "calificacion": 9.5
}
```

---

### 6. Endpoint combinado

**Método:** GET

```text
/alumnos/{id}/info
```

Ejemplo:

```text
/alumnos/1/info
```

Este endpoint combina:

- Datos internos del alumno almacenados en MySQL.
- Datos externos obtenidos desde una API pública.

## Tecnologías utilizadas

- Docker
- Docker Compose
- Apache
- PHP
- MySQL
- phpMyAdmin
- API pública externa

## Puertos

- Backend: `8080`
- phpMyAdmin: `8081`
- MySQL: `3306`