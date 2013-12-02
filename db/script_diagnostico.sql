INSERT INTO `formularios` (`id`, `nombre`, `numero`) VALUES 
('1', 'Diagnóstico', '1');

INSERT INTO `formularios` (`id`, `nombre`, `numero`, `formulario_id`) VALUES 
(2, 'Información general', '1', '1'),
(3, 'Habilidades y preferencias', '2', '1'),
(4, 'Información recabada', '3', '1'),
(5, 'Información recabada', '4', '1'),
(6, 'Decisión consciente', '5', '1');


UPDATE `formularios` SET `descripcion`='<p>Realiza el siguiente test gratuito que te indicará en qué parte del proceso para escoger carrera te encuentras y qué recomendaciones te sugerimos para escoger la carrera más acorde a tu perfil reduciendo las posibilidades de equivocarte.</p><p>A continuación te presentamos una serie de afirmaciones relacionadas con tu proceso de decisión de carrera. Te recordamos que todas las respuestas son válidas y no existen respuestas correctas o incorrectas.</p>', `encabezado`='Este diagnostico es Gratuito y evalúa el estado en el cual te encuentras y a su ves dará recomendaciones para comenzar con la búsqueda de tu vocación ideal.' WHERE `id`='1';
UPDATE `formularios` SET `descripcion`='Para cada afirmación a continuación  debes desplazar la barra de interés en una escala de 1 a 5 (las opciones de respuesta significa 1 muy poco y 5 mucho).' WHERE `id`='3';
UPDATE `formularios` SET `descripcion`='Para cada afirmación debes responder en una escala de 1 a 3. Cada una de las opciones de respuesta significa lo siguiente: 1 Nada, 2 Algo y 3 Mucho' WHERE `id`='4';
UPDATE `formularios` SET `descripcion`='Para cada afirmación debes responder en una escala de 1 a 3. Cada una de las opciones de respuesta significa lo siguiente: 1 Nada, 2 Algo y 3 Mucho' WHERE `id`='5';
UPDATE `formularios` SET `descripcion`='Para cada afirmación a continuación  debes desplazar la barra de interés en una escala de 1 a 5 (las opciones de respuesta significa 1 muy poco y 5 mucho).' WHERE `id`='6';
UPDATE `formularios` SET `descripcion`='<p>Por favor escribe el correo de las personas que consideras te conocen, la opinión de ellos puede ayudar en tu decisión de carrera. Como mínimo deberás lograr que tres personas las diligencien, si logras un mayor número tendrás mejor puntaje.</p><p>Te recomendamos que por lo menos esta encuesta sea diligenciada por un padre o familiar cercano, por un amigo y por un profesor o tutor, para que en realidad sea una encuesta 360°.</p><p>Tan pronto ingreses el correo de la persona que seleccionaste, nosotros le enviaremos un correo con las indicaciones, sin embargo te solicitamos que te contactes con estas personas para que estén enteradas.</p>' WHERE `id`='9';



INSERT INTO `preguntas` (`id`, `pregunta`, `numero`, `preguntastipo_id`, `formulario_id`) VALUES 
('1', 'Selecciona una de las siguientes afirmaciones que se ajuste a la razón por la cual estas interesado en nuestro programa.', '1', '1', '2'),
('2', 'Selecciona de mayor a menor una de las siguientes afirmaciones que se ajuste a la razón por la cual estas interesado en estudiar. 1 es el mayor y 4 el menor.', '2', '3', '2'),
('3', 'Qué tan importante es esta decisión de carrera/vocación para usted', '3', '6', '2'),
('4', 'La decisión de estudio está vinculada a la satisfacción que sienta en general conmigo mismo', '4', '4', '2'),
('5', 'Para mi la decisión de estudio puede influenciar el qué tan satisfecho me sienta durante la carrera', '5', '4', '2'),
('6', 'Pienso que las calificaciones que recibiré en la carrera que decida estudiar pueden tener relación con la  facilidad de conseguir empleo', '6', '4', '2'),
('7', 'Creo que la elección de la carrera influirá en las opciones de trabajo que se me presenten a futuro', '7', '4', '2'),
('8', 'Creo que la elección de la carrera influirá en tener mayores ingresos salariales', '8', '4', '2');

INSERT INTO `opciones` (`nombre`, `pregunta_id`) VALUES 
('Ninguna carrera me ha llamado la atención hasta ahora', '1'),
('Tengo varias opciones de carrera pero no sé cuál escoger', '1'),
('Empecé una carrera pero no estoy satisfecho con la decisión que tomé', '1'),
('Tengo claridad sobre qué quiero estudiar pero quiero reafirmar mi decisión', '1'),
('Ingresos salariales, importancia y prestigio', '2'),
('Conseguir un buen trabajo y de forma fácil', '2'),
('Cumplir las expectativas o seguir el ejemplo de familiares, amigos o profesores', '2'),
('Me gustaría hacer lo que me apasione', '2');


INSERT INTO `preguntas` (`id`, `pregunta`, `numero`, `preguntastipo_id`, `formulario_id`) VALUES 
('9', 'Se me facilita reconocer aquellas actividades que realizo mejor', '1', '7', '3'),
('10', 'Logro identificar con gran exactitud aquellas materias o asignaturas que me interesan', '2', '7', '3'),
('11', 'Se me facilita reconocer aspectos de mi forma de ser o de mis habilidades y mas aun aspectos que me apasionan para encontrar su relación con diferentes carreras.', '3', '7', '3'),
('12', 'Mis habilidades, preferencias en cuanto a las actividades que me apasionan cuadran con mis alternativas de estudio', '4', '7', '3'),
('13', 'He leído y recurrido a información diferente relacionada con opciones de carrera', '1', '5', '4'),
('14', 'Conozco las diferencias y ventajas que existen entre las carreras técnicas profesionales, tecnológicas y universitarias', '2', '5', '4'),
('15', 'He consultado fuentes serias (estadísticas o bases de datos por ejemplo) que informan sobre salarios, no. de egresados, oferta laboral entre otros temas', '3', '5', '4'),
('16', 'Entiendo a la perfección cómo saber si una Institución de Educación Superior cuenta con acreditación del Ministerio de Educación', '4', '5', '4'),
('17', 'Que tan profundo ha indagado  las carreras que le interesan', '1', '5', '5'),
('18', 'Que tan profundo ha indagado  los diferentes costos de matrícula', '2', '5', '5'),
('19', 'Que tan profundo has investigado por Internet sobre las diferentes instituciones de educación superior', '3', '5', '5'),
('20', 'Que tanto has revisado los pensum de los programas en las universidades que te interesan', '4', '5', '5'),
('21', 'He realizado una evaluación detallada de los diferentes programas académicos que se relacionan con mis preferencias', '5', '5', '5'),
('22', 'Tengo claridad sobre mis objetivos académicos y profesionales a corto, mediano y largo plazo', '1', '7', '6'),
('23', 'Sé cómo elaborar un presupuesto que incluye todos los gastos adicionales a la matrícula que implica el ingresar a la universidad', '2', '7', '6'),
('24', 'Me interesa conversar con familiares o allegados acerca de su opinión sobre mi decisión de carrera', '3', '7', '6'),
('25', 'Considero que la opinión de mis profesores es importante y por eso tengo en cuenta lo que ellos creen que es lo mejor para mí.', '4', '7', '6'),
('26', 'He conversado con personas que estudiaron aquello que me interesaría estudiar', '5', '7', '6'),
('27', 'He realizado una evaluación detallada respecto a las alternativas de estudio que estoy contemplando para decidir cual cumple con la mayoría de mis preferencias', '6', '7', '6'),
('28', 'Tengo ya definida una alternativa de estudio con la cual me siento tranquilo y satisfecho', '7', '7', '6');


INSERT INTO `formularios` (`id`, `nombre`, `numero`, `encabezado`) VALUES 
('7', 'Mentor experto', '2', 'Desde este momento usted tendrá que elegir su Asesor experto en Orientación Vocacional. Sera el mentor que lo acompañara en todo el proceso, por lo tanto revise muy bien la disponibilidad y su perfil para que estos cumplan con sus intereses.'),
('8', 'Test vocacional', '3', '', 'Esta prueba esta desarrollada por una empresa dedicada a la generación de pruebas psicológicas y será analizada por el experto que has escogido como guía de tu programa. Después de la próxima asesoría podrás ver los resultados.'),
('9', 'Evaluación 360', '4', 'Recuerda que entre más personas te den retroalimentación tendrás mayor conocimiento sobre tus fortalezas y debilidades.'),
('10', 'Diseño de vida', '5', 'Esta prueba te ayudara a entender cuales son tus intereses a largo plazo y como estos se ajustan a tus decisiones actuales. Toma todo el tiempo que sea necesario para llenar esta prueba.'),
('11', 'Mercado laboral', '6', 'Programa de Información: Si tienes claras cuales son tus habilidades, fortalezas y destrezas y tienes claros tu panorama a corto, mediano y largo plazo, selecciona la alternativa de estudio (solo una) que quieres estudiar para ver como esta el mercado laboral. Si no tienes claro alguna de las variables para evaluar alternativas de la prueba de ponderación te recomendamos hacer el programa de orientación'),
('12', 'Red de mentores', '7', 'Programa de Información: Según las Alternativas de estudio avaluadas con tu Experto OV, busca por lo menos 3 profesionales para cada alternativa de estudio. En lo posible escoge con diferentes años de experiencia y en las profundizaciones o especializaciones que te llaman la atención.'),
('13', 'Ponderación', '8', 'Esta ponderación le dará las herramientas para seleccionar su mejor alternativas. Si alguna variable no es importante para usted déjela en 0. Si siente que no tiene la información suficiente para diligenciar esta matriz le recomendamos realizar el programa de orientación y si este ya lo realizo, aclarar las dudas con mentorias de profesionales.'),
('14', 'Universidad', '9', 'Le solicitaremos una información, para poder enviarle un reporte de las universidades que debería contemplar en su selección de institución educativa.');


-- Sub formularios de evaluacion 360
INSERT INTO `formularios` (`id`, `nombre`, `numero`, `formulario_id`) VALUES 
('15', 'Personales', '1', '9'),
('16', 'Sociales', '2', '9'),
('17', 'Sociales', '3', '9'),
('18', 'Administrativas', '4', '9'),
('19', 'Cognitivas', '5', '9'),
('20', 'Cognitivas', '6', '9'),
('21', 'Cognitivas', '7', '9');

-- Sub formularios de diseño de vida
INSERT INTO `formularios` (`id`, `nombre`, `numero`, `formulario_id`) VALUES 
('22', 'Intereses académicos', '1', '10'),
('23', 'Intereses de carrera', '2', '10'),
('24', 'Agronomía y veterinaria', '3', '10'),
('25', 'Bellas artes', '4', '10'),
('26', 'Ciencias de la salud', '5', '10'),
('27', 'Ciencias sociales, derecho y ciencias políticas', '6', '10'),
('28', 'Ciencias de la ecucación', '7', '10'),
('29', 'Economía, administración y contaduría', '8', '10'),
('30', 'Ingeniería, arquitectura y urbanismo', '9', '10'),
('31', 'Matemáticas y ciencias naturales', '10', '10'),
('32', 'Intereses de carrera 2', '11', '10'),
('33', 'Valores ocupacionales', '12', '10'),
('34', 'Valores ocupacionales', '13', '10'),
('35', 'Análisis', '14', '10'),
('36', 'Vida', '15', '10'),
('37', 'Metas', '16', '10');

-- Sub formularios de ponderacion
INSERT INTO `formularios` (`id`, `nombre`, `numero`, `formulario_id`) VALUES 
('38', 'Ponderación consciente (Paso 1)', '1', '13'),
('39', 'Ponderación consciente (Paso 2)', '2', '13'),
('40', 'Ponderación consciente (Paso 3)', '3', '13');


