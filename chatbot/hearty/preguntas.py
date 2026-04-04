FLUJO_PREGUNTAS = [
    {
        "id": "bienvenida",
        "mensaje": "¡Hola! Soy Hearty, tu guía emocional 💚 Estoy aquí para acompañarte. ¿Cómo te sientes hoy?",
        "opciones": ["😊 Bien", "😌 Tranquilo/a", "😰 Ansioso/a", "😢 Triste", "😠 Enfadado/a", "😴 Cansado/a"],
        "siguiente": "profundizar"
    },
    {
        "id": "profundizar",
        "mensaje": "Gracias por contármelo. ¿Hace cuánto tiempo te sientes así?",
        "opciones": ["Hoy solamente", "Unos días", "Varias semanas", "Hace mucho tiempo"],
        "siguiente": "causa"
    },
    {
        "id": "causa",
        "mensaje": "Entiendo. ¿Crees que hay algo concreto que esté influyendo en cómo te sientes?",
        "opciones": ["El trabajo o estudios", "Mis relaciones personales", "Mi salud", "No lo sé", "Varias cosas a la vez"],
        "siguiente": "apoyo"
    },
    {
        "id": "apoyo",
        "mensaje": "¿Qué tipo de apoyo te vendría mejor ahora mismo?",
        "opciones": ["Técnicas para calmarme", "Recursos informativos", "Solo quiero desahogarme", "Consejos prácticos"],
        "siguiente": "final"
    }
]