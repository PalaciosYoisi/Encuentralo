ENCUÉNTRALO V6 - IA SEMÁNTICA + VISUAL

Cambios principales:
1. MobileNet analiza la foto y guarda hasta 10 predicciones reales.
2. Las etiquetas de IA se normalizan en familias visuales (cámara, celular, bolso, silla, etc.).
3. Dos objetos de la misma familia pueden aparecer como candidatos aunque sean marcas/modelos diferentes.
4. Una cámara y un bolso se descartan aunque compartan color, lugar, fecha o categoría escrita.
5. La categoría elegida por el usuario NO decide la coincidencia; solo aporta como máximo 1 punto de contexto.
6. Una fotografía idéntica conserva detección casi total.
7. Las coincidencias principales siguen siendo PERDIDO <-> ENCONTRADO.
8. No requiere nuevas columnas respecto a V5 (incluye image_hash).

IMPORTANTE: reportes creados antes de guardar ai_embedding/ai_label pueden no tener suficiente información de IA. Para una prueba limpia, cree dos reportes nuevos con fotos distintas del mismo tipo de objeto, uno PERDIDO y otro ENCONTRADO.
