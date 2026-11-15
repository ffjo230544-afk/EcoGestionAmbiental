(() => {
  "use strict";

  const root = document.querySelector("[data-game-root]");

  if (!root) {
    return;
  }

  const gameType = root.dataset.gameType || "";
  const endpoint = root.dataset.gameEndpoint || "";
  const csrf = root.querySelector("[data-game-csrf]")?.value || "";
  const messageElement = root.querySelector("[data-game-message]");
  const startedAt = Date.now();
  let finished = false;

  const timerElement = root.querySelector("[data-game-timer]");

  const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60)
      .toString()
      .padStart(2, "0");

    const remaining = (seconds % 60)
      .toString()
      .padStart(2, "0");

    return `${minutes}:${remaining}`;
  };

  const updateTimer = () => {
    if (!timerElement || finished) {
      return;
    }

    const seconds = Math.floor((Date.now() - startedAt) / 1000);
    timerElement.textContent = formatTime(seconds);
  };

  updateTimer();
  const timerId = window.setInterval(updateTimer, 1000);

  const showMessage = (message, type = "error") => {
    if (!messageElement) {
      return;
    }

    messageElement.textContent = message;
    messageElement.className = `game-inline-message ${type}`;
    messageElement.classList.remove("d-none");
    messageElement.scrollIntoView({ behavior: "smooth", block: "nearest" });
  };

  const clearMessage = () => {
    messageElement?.classList.add("d-none");
  };

  const submitResult = async (answers) => {
    if (endpoint === "" || csrf === "") {
      throw new Error("No fue posible preparar el envío del resultado.");
    }

    const body = new URLSearchParams({
      _csrf: csrf,
      juego: gameType,
      respuestas: JSON.stringify(answers),
    });

    const response = await window.fetch(endpoint, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
        Accept: "application/json",
      },
      body: body.toString(),
      credentials: "same-origin",
    });

    let data;

    try {
      data = await response.json();
    } catch {
      throw new Error("El servidor devolvió una respuesta inesperada.");
    }

    if (!response.ok || !data.success) {
      throw new Error(data.message || "No fue posible guardar el resultado.");
    }

    return data;
  };

  const createConfetti = () => {
    const colors = ["#16803c", "#a3e635", "#38bdf8", "#fbbf24", "#f472b6"];
    const container = document.createElement("div");
    container.className = "game-confetti";
    container.setAttribute("aria-hidden", "true");

    for (let index = 0; index < 36; index += 1) {
      const piece = document.createElement("span");
      piece.style.left = `${Math.random() * 100}%`;
      piece.style.backgroundColor = colors[index % colors.length];
      piece.style.animationDelay = `${Math.random() * 0.8}s`;
      piece.style.animationDuration = `${1.8 + Math.random() * 1.4}s`;
      container.append(piece);
    }

    document.body.append(container);
    window.setTimeout(() => container.remove(), 3600);
  };

  const showResult = (data, feedbackRenderer) => {
    finished = true;
    window.clearInterval(timerId);

    const panel = root.querySelector("[data-game-result]");

    if (!panel) {
      return;
    }

    const score = Number(data.score || 0);
    const medal = score === 100 ? "🏆" : score >= 80 ? "🌳" : score >= 60 ? "🌿" : "🌱";
    const title = score === 100
      ? "¡Puntuación perfecta!"
      : score >= 80
        ? "¡Excelente trabajo!"
        : score >= 60
          ? "¡Vas por buen camino!"
          : "¡Cada intento te enseña!";

    panel.querySelector("[data-result-medal]").textContent = medal;
    panel.querySelector("[data-result-title]").textContent = title;
    panel.querySelector("[data-result-score]").textContent = score.toString();
    panel.querySelector("[data-result-message]").textContent = data.message;
    panel.querySelector("[data-result-correct]").textContent = data.correct;
    panel.querySelector("[data-result-errors]").textContent = data.errors;

    const feedback = panel.querySelector("[data-result-feedback]");

    if (feedback && typeof feedbackRenderer === "function") {
      feedback.replaceChildren();
      feedbackRenderer(feedback, data.feedback || []);
    }

    panel.classList.remove("d-none");
    panel.scrollIntoView({ behavior: "smooth", block: "start" });

    if (score >= 80) {
      createConfetti();
    }
  };

  root.querySelector("[data-game-restart]")?.addEventListener("click", () => {
    window.location.reload();
  });

  const initializeClassification = () => {
    const bank = root.querySelector("[data-item-bank]");
    const bins = [...root.querySelectorAll("[data-category]")];
    const items = [...root.querySelectorAll("[data-item-id]")];
    const submitButton = root.querySelector("[data-classification-submit]");
    const countElement = root.querySelector("[data-classified-count]");
    const selectedHint = root.querySelector("[data-selected-hint]");
    const assignments = {};
    let selectedItem = null;

    if (!bank || bins.length === 0 || items.length === 0 || !submitButton) {
      return;
    }

    [...items]
      .sort(() => Math.random() - 0.5)
      .forEach((item) => bank.append(item));

    const refreshBins = () => {
      bins.forEach((bin) => {
        const hasItems = Boolean(bin.querySelector("[data-item-id]"));
        bin.querySelector("[data-bin-empty]")?.classList.toggle("d-none", hasItems);
      });

      if (countElement) {
        countElement.textContent = Object.keys(assignments).length.toString();
      }
    };

    const selectItem = (item) => {
      items.forEach((current) => current.classList.remove("selected"));

      if (selectedItem === item) {
        selectedItem = null;

        if (selectedHint) {
          selectedHint.textContent = "Ningún objeto seleccionado";
        }

        return;
      }

      selectedItem = item;
      item.classList.add("selected");

      if (selectedHint) {
        selectedHint.textContent = `Seleccionado: ${item.textContent.trim()}`;
      }
    };

    const placeItem = (item, bin) => {
      const itemId = item.dataset.itemId;
      const category = bin.dataset.category;
      const destination = bin.querySelector("[data-bin-items]");

      if (!itemId || !category || !destination || finished) {
        return;
      }

      destination.append(item);
      assignments[itemId] = category;
      item.classList.remove("selected");
      selectedItem = null;

      if (selectedHint) {
        selectedHint.textContent = "Ningún objeto seleccionado";
      }

      bin.classList.add("received");
      window.setTimeout(() => bin.classList.remove("received"), 350);
      clearMessage();
      refreshBins();
    };

    items.forEach((item) => {
      item.addEventListener("click", (event) => {
        event.stopPropagation();
        selectItem(item);
      });

      item.addEventListener("dragstart", (event) => {
        if (finished) {
          event.preventDefault();
          return;
        }

        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData("text/plain", item.dataset.itemId || "");
        item.classList.add("dragging");
      });

      item.addEventListener("dragend", () => {
        item.classList.remove("dragging");
      });
    });

    bins.forEach((bin) => {
      bin.addEventListener("click", (event) => {
        if (event.target.closest("[data-item-id]")) {
          return;
        }

        if (selectedItem) {
          placeItem(selectedItem, bin);
        }
      });

      bin.addEventListener("dragover", (event) => {
        event.preventDefault();
        event.dataTransfer.dropEffect = "move";
        bin.classList.add("drag-over");
      });

      bin.addEventListener("dragleave", () => {
        bin.classList.remove("drag-over");
      });

      bin.addEventListener("drop", (event) => {
        event.preventDefault();
        bin.classList.remove("drag-over");

        const itemId = event.dataTransfer.getData("text/plain");
        const item = root.querySelector(
          `[data-item-id="${window.CSS.escape(itemId)}"]`
        );

        if (item) {
          placeItem(item, bin);
        }
      });
    });

    submitButton.addEventListener("click", async () => {
      clearMessage();

      if (Object.keys(assignments).length !== items.length) {
        showMessage("Clasifica todos los residuos antes de revisar el resultado.");
        return;
      }

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Revisando';

      try {
        const data = await submitResult(assignments);

        items.forEach((item) => {
          item.draggable = false;
          item.disabled = true;
        });

        const feedbackById = new Map(
          data.feedback.map((item) => [item.id, item])
        );

        items.forEach((item) => {
          const feedback = feedbackById.get(item.dataset.itemId);

          if (!feedback) {
            return;
          }

          item.classList.add(feedback.correct ? "answer-correct" : "answer-wrong");
          item.title = feedback.correct
            ? "Clasificación correcta"
            : `Debe ir en: ${feedback.expected}`;
        });

        showResult(data, (container, feedbackItems) => {
          feedbackItems
            .filter((item) => !item.correct)
            .forEach((item) => {
              const row = document.createElement("div");
              row.className = "game-feedback-item wrong";

              const icon = document.createElement("i");
              icon.className = "bi bi-arrow-right-circle-fill";

              const text = document.createElement("span");
              text.textContent = `${item.name}: corresponde a ${item.expected}.`;

              row.append(icon, text);
              container.append(row);
            });

          if (!container.hasChildNodes()) {
            const row = document.createElement("div");
            row.className = "game-feedback-item correct";
            row.innerHTML = '<i class="bi bi-stars"></i><span>¡Clasificaste correctamente todos los residuos!</span>';
            container.append(row);
          }
        });
      } catch (error) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Revisar resultado';
        showMessage(error.message);
      }
    });

    refreshBins();
  };

  const initializeTrivia = () => {
    const questions = [...root.querySelectorAll("[data-trivia-question]")];
    const previousButton = root.querySelector("[data-trivia-previous]");
    const nextButton = root.querySelector("[data-trivia-next]");
    const submitButton = root.querySelector("[data-trivia-submit]");
    const currentElement = root.querySelector("[data-trivia-current]");
    const progressElement = root.querySelector("[data-trivia-progress]");
    const progressText = root.querySelector("[data-trivia-progress-text]");
    const quizCard = root.querySelector(".trivia-question-card");
    let currentIndex = 0;

    if (
      questions.length === 0
      || !previousButton
      || !nextButton
      || !submitButton
    ) {
      return;
    }

    const showQuestion = (index) => {
      currentIndex = Math.max(0, Math.min(index, questions.length - 1));

      questions.forEach((question, questionIndex) => {
        question.classList.toggle("d-none", questionIndex !== currentIndex);
      });

      previousButton.disabled = currentIndex === 0;
      nextButton.classList.toggle("d-none", currentIndex === questions.length - 1);
      submitButton.classList.toggle("d-none", currentIndex !== questions.length - 1);

      const percentage = Math.round(((currentIndex + 1) / questions.length) * 100);

      if (currentElement) {
        currentElement.textContent = (currentIndex + 1).toString();
      }

      if (progressElement) {
        progressElement.style.width = `${percentage}%`;
      }

      if (progressText) {
        progressText.textContent = `${percentage}%`;
      }

      clearMessage();
    };

    const selectedAnswer = (question) => {
      return question.querySelector('input[type="radio"]:checked');
    };

    questions.forEach((question) => {
      question.querySelectorAll('input[type="radio"]').forEach((input) => {
        input.addEventListener("change", () => {
          question.querySelectorAll(".trivia-option").forEach((option) => {
            option.classList.toggle(
              "selected",
              Boolean(option.querySelector('input[type="radio"]:checked'))
            );
          });

          clearMessage();
        });
      });
    });

    nextButton.addEventListener("click", () => {
      if (!selectedAnswer(questions[currentIndex])) {
        showMessage("Selecciona una respuesta para continuar.");
        return;
      }

      showQuestion(currentIndex + 1);
    });

    previousButton.addEventListener("click", () => {
      showQuestion(currentIndex - 1);
    });

    submitButton.addEventListener("click", async () => {
      const answers = {};
      let incompleteIndex = -1;

      questions.forEach((question, index) => {
        const selected = selectedAnswer(question);

        if (!selected && incompleteIndex === -1) {
          incompleteIndex = index;
          return;
        }

        answers[question.dataset.questionId] = selected?.value || "";
      });

      if (incompleteIndex >= 0) {
        showQuestion(incompleteIndex);
        showMessage("Responde esta pregunta antes de finalizar.");
        return;
      }

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Calculando';

      try {
        const data = await submitResult(answers);
        quizCard?.classList.add("d-none");

        showResult(data, (container, feedbackItems) => {
          feedbackItems.forEach((item, index) => {
            const row = document.createElement("article");
            row.className = `game-feedback-item ${item.correct ? "correct" : "wrong"}`;

            const icon = document.createElement("i");
            icon.className = item.correct
              ? "bi bi-check-circle-fill"
              : "bi bi-lightbulb-fill";

            const content = document.createElement("div");
            const title = document.createElement("strong");
            title.textContent = item.correct
              ? `Pregunta ${index + 1}: correcta`
              : `Pregunta ${index + 1}: ${item.correct_answer}`;

            const explanation = document.createElement("p");
            explanation.textContent = item.explanation;

            content.append(title, explanation);
            row.append(icon, content);
            container.append(row);
          });
        });
      } catch (error) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Finalizar trivia';
        showMessage(error.message);
      }
    });

    showQuestion(0);
  };

  const renderLearningFeedback = (container, feedbackItems) => {
    feedbackItems.forEach((item) => {
      const row = document.createElement("article");
      row.className = "game-feedback-item correct";

      const icon = document.createElement("i");
      icon.className = "bi bi-lightbulb-fill";

      const content = document.createElement("div");
      const title = document.createElement("strong");
      title.textContent = item.title;

      const explanation = document.createElement("p");
      explanation.textContent = item.explanation;

      content.append(title, explanation);
      row.append(icon, content);
      container.append(row);
    });
  };

  const initializeMemory = () => {
    const board = root.querySelector("[data-memory-board]");
    const cards = [...root.querySelectorAll("[data-memory-card]")];
    const matchesElement = root.querySelector("[data-memory-matches]");
    const attemptsElement = root.querySelector("[data-memory-attempts]");
    const moves = [];
    let openCards = [];
    let matches = 0;
    let attempts = 0;
    let checking = false;
    let submitting = false;

    if (!board || cards.length === 0) {
      return;
    }

    const refreshCounters = () => {
      if (matchesElement) {
        matchesElement.textContent = matches.toString();
      }

      if (attemptsElement) {
        attemptsElement.textContent = attempts.toString();
      }
    };

    const finishMemory = async () => {
      if (submitting) {
        return;
      }

      submitting = true;
      board.classList.add("completed");

      try {
        const data = await submitResult(moves);
        showResult(data, renderLearningFeedback);
      } catch (error) {
        submitting = false;
        board.classList.remove("completed");
        showMessage(error.message);
      }
    };

    cards.forEach((card) => {
      card.addEventListener("click", () => {
        if (
          checking
          || submitting
          || card.classList.contains("flipped")
          || card.classList.contains("matched")
        ) {
          return;
        }

        clearMessage();
        card.classList.add("flipped");
        card.setAttribute("aria-pressed", "true");
        openCards.push(card);

        if (openCards.length < 2) {
          return;
        }

        checking = true;
        attempts += 1;
        refreshCounters();

        const [firstCard, secondCard] = openCards;
        moves.push([
          firstCard.dataset.cardId || "",
          secondCard.dataset.cardId || "",
        ]);

        const isPair = firstCard.dataset.matchToken
          && firstCard.dataset.matchToken === secondCard.dataset.matchToken;

        window.setTimeout(() => {
          if (isPair) {
            firstCard.classList.add("matched");
            secondCard.classList.add("matched");
            firstCard.disabled = true;
            secondCard.disabled = true;
            matches += 1;
          } else {
            firstCard.classList.remove("flipped");
            secondCard.classList.remove("flipped");
            firstCard.setAttribute("aria-pressed", "false");
            secondCard.setAttribute("aria-pressed", "false");
            board.classList.add("memory-miss");
            window.setTimeout(
              () => board.classList.remove("memory-miss"),
              350
            );
          }

          openCards = [];
          checking = false;
          refreshCounters();

          if (matches === cards.length / 2) {
            finishMemory();
          }
        }, 750);
      });
    });

    refreshCounters();
  };

  const initializeCleanPark = () => {
    const scene = root.querySelector("[data-park-scene]");
    const objects = [...root.querySelectorAll("[data-park-object]")];
    const collectedElement = root.querySelector("[data-park-collected]");
    const careElement = root.querySelector("[data-park-care]");
    const bagElement = root.querySelector("[data-park-bag-count]");
    const progressElement = root.querySelector("[data-park-progress]");
    const progressText = root.querySelector("[data-park-progress-text]");
    const liveMessage = root.querySelector("[data-park-live]");
    const wasteTotal = Number(root.dataset.wasteTotal || 0);
    const clicks = [];
    const collected = new Set();
    let errors = 0;
    let submitting = false;

    if (!scene || objects.length === 0 || wasteTotal < 1) {
      return;
    }

    const updateProgress = () => {
      const percentage = Math.round((collected.size / wasteTotal) * 100);
      const care = Math.max(0, 100 - (errors * 10));

      if (collectedElement) {
        collectedElement.textContent = collected.size.toString();
      }

      if (bagElement) {
        bagElement.textContent = collected.size.toString();
      }

      if (careElement) {
        careElement.textContent = `${care}%`;
      }

      if (progressElement) {
        progressElement.style.width = `${percentage}%`;
      }

      if (progressText) {
        progressText.textContent = `${percentage}%`;
      }

      scene.classList.toggle("park-half-clean", percentage >= 50);
      scene.classList.toggle("park-clean", percentage >= 100);
    };

    const announce = (message, type) => {
      if (!liveMessage) {
        return;
      }

      liveMessage.className = `park-live-message ${type}`;
      liveMessage.querySelector("span").textContent = message;
      liveMessage.querySelector("i").className = type === "success"
        ? "bi bi-check-circle-fill"
        : "bi bi-shield-exclamation";
    };

    const finishPark = async () => {
      if (submitting) {
        return;
      }

      submitting = true;
      objects.forEach((object) => {
        object.disabled = true;
      });

      announce("¡Misión terminada! Estamos calculando tu resultado.", "success");

      try {
        const data = await submitResult(clicks);
        showResult(data, renderLearningFeedback);
      } catch (error) {
        submitting = false;
        objects
          .filter((object) => !object.classList.contains("collected"))
          .forEach((object) => {
            object.disabled = false;
          });
        showMessage(error.message);
      }
    };

    objects.forEach((object) => {
      object.addEventListener("click", () => {
        if (submitting || object.classList.contains("collected")) {
          return;
        }

        const objectId = object.dataset.objectId || "";
        const objectType = object.dataset.objectType || "";
        clicks.push(objectId);
        clearMessage();

        if (objectType === "waste") {
          collected.add(objectId);
          object.classList.add("collected");
          object.disabled = true;
          announce(
            `¡Muy bien! Recogiste ${object.getAttribute("aria-label")}.`,
            "success"
          );
        } else {
          errors += 1;
          object.classList.remove("protected-warning");
          void object.offsetWidth;
          object.classList.add("protected-warning");
          announce(
            `${object.getAttribute("aria-label")} pertenece a la naturaleza. Déjalo en el parque.`,
            "warning"
          );
        }

        updateProgress();

        if (collected.size === wasteTotal) {
          window.setTimeout(finishPark, 650);
        }
      });
    });

    updateProgress();
  };

  const initializeRecyclingRoute = () => {
    const lanes = [...root.querySelectorAll("[data-route-lane]")];
    const submitButton = root.querySelector("[data-route-submit]");
    let draggedCard = null;

    if (lanes.length === 0 || !submitButton) {
      return;
    }

    const refreshPositions = (list) => {
      const cards = [...list.querySelectorAll("[data-route-stage]")];

      cards.forEach((card, index) => {
        const position = card.querySelector("[data-stage-position]");
        const upButton = card.querySelector("[data-stage-up]");
        const downButton = card.querySelector("[data-stage-down]");

        if (position) {
          position.textContent = (index + 1).toString();
        }

        if (upButton) {
          upButton.disabled = index === 0;
        }

        if (downButton) {
          downButton.disabled = index === cards.length - 1;
        }
      });
    };

    const cardAfterPointer = (list, pointerY) => {
      const cards = [
        ...list.querySelectorAll("[data-route-stage]:not(.dragging)"),
      ];

      return cards.reduce(
        (closest, card) => {
          const box = card.getBoundingClientRect();
          const offset = pointerY - box.top - (box.height / 2);

          if (offset < 0 && offset > closest.offset) {
            return { offset, card };
          }

          return closest;
        },
        { offset: Number.NEGATIVE_INFINITY, card: null }
      ).card;
    };

    lanes.forEach((lane) => {
      const list = lane.querySelector("[data-route-list]");

      if (!list) {
        return;
      }

      const cards = [...list.querySelectorAll("[data-route-stage]")];

      cards.forEach((card) => {
        card.addEventListener("dragstart", (event) => {
          if (finished) {
            event.preventDefault();
            return;
          }

          draggedCard = card;
          card.classList.add("dragging");
          event.dataTransfer.effectAllowed = "move";
          event.dataTransfer.setData("text/plain", card.dataset.stageId || "");
        });

        card.addEventListener("dragend", () => {
          card.classList.remove("dragging");
          lane.classList.remove("drag-active");
          draggedCard = null;
          refreshPositions(list);
        });

        card.querySelector("[data-stage-up]")?.addEventListener("click", () => {
          const previous = card.previousElementSibling;

          if (previous) {
            list.insertBefore(card, previous);
            refreshPositions(list);
            clearMessage();
          }
        });

        card.querySelector("[data-stage-down]")?.addEventListener("click", () => {
          const next = card.nextElementSibling;

          if (next) {
            list.insertBefore(next, card);
            refreshPositions(list);
            clearMessage();
          }
        });
      });

      list.addEventListener("dragover", (event) => {
        event.preventDefault();

        if (!draggedCard || !list.contains(draggedCard)) {
          return;
        }

        lane.classList.add("drag-active");
        const afterCard = cardAfterPointer(list, event.clientY);

        if (afterCard) {
          list.insertBefore(draggedCard, afterCard);
        } else {
          list.append(draggedCard);
        }
      });

      list.addEventListener("drop", (event) => {
        event.preventDefault();
        lane.classList.remove("drag-active");
        refreshPositions(list);
      });

      refreshPositions(list);
    });

    submitButton.addEventListener("click", async () => {
      const answers = {};

      lanes.forEach((lane) => {
        answers[lane.dataset.routeId] = [
          ...lane.querySelectorAll("[data-route-stage]"),
        ].map((card) => card.dataset.stageId || "");
      });

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Revisando rutas';
      clearMessage();

      try {
        const data = await submitResult(answers);

        root.querySelectorAll("[data-route-stage]").forEach((card) => {
          card.draggable = false;
          card.querySelectorAll("button").forEach((button) => {
            button.disabled = true;
          });
        });

        showResult(data, (container, feedbackItems) => {
          feedbackItems.forEach((item) => {
            const article = document.createElement("article");
            article.className = item.correct === item.total
              ? "route-feedback-card complete"
              : "route-feedback-card";

            const heading = document.createElement("div");
            const title = document.createElement("strong");
            const score = document.createElement("span");
            title.textContent = item.title;
            score.textContent = `${item.correct}/${item.total} en posición correcta`;
            heading.append(title, score);

            const order = document.createElement("ol");
            item.order.forEach((stage) => {
              const row = document.createElement("li");
              row.textContent = stage;
              order.append(row);
            });

            article.append(heading, order);
            container.append(article);
          });
        });
      } catch (error) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Revisar las rutas';
        showMessage(error.message);
      }
    });
  };

  const initializeDecisions = () => {
    const questions = [...root.querySelectorAll("[data-decision-question]")];
    const layout = root.querySelector("[data-decisions-layout]");
    const previousButton = root.querySelector("[data-decision-previous]");
    const nextButton = root.querySelector("[data-decision-next]");
    const submitButton = root.querySelector("[data-decision-submit]");
    const currentElement = root.querySelector("[data-decision-current]");
    const answeredElement = root.querySelector("[data-decision-answered]");
    const roomElement = root.querySelector("[data-decision-room]");
    const progressElement = root.querySelector("[data-decision-progress]");
    const progressText = root.querySelector("[data-decision-progress-text]");
    let currentIndex = 0;

    if (
      questions.length === 0
      || !previousButton
      || !nextButton
      || !submitButton
    ) {
      return;
    }

    const selectedAnswer = (question) => {
      return question.querySelector('input[type="radio"]:checked');
    };

    const refreshAnswered = () => {
      const answered = questions.filter((question) => selectedAnswer(question)).length;

      if (answeredElement) {
        answeredElement.textContent = answered.toString();
      }
    };

    const showQuestion = (index) => {
      currentIndex = Math.max(0, Math.min(index, questions.length - 1));

      questions.forEach((question, questionIndex) => {
        question.classList.toggle("d-none", questionIndex !== currentIndex);
      });

      previousButton.disabled = currentIndex === 0;
      nextButton.classList.toggle("d-none", currentIndex === questions.length - 1);
      submitButton.classList.toggle("d-none", currentIndex !== questions.length - 1);

      const percentage = Math.round(((currentIndex + 1) / questions.length) * 100);

      if (currentElement) {
        currentElement.textContent = (currentIndex + 1).toString();
      }

      if (roomElement) {
        roomElement.textContent = questions[currentIndex].dataset.room || "EcoCasa";
      }

      if (progressElement) {
        progressElement.style.width = `${percentage}%`;
      }

      if (progressText) {
        progressText.textContent = `${percentage}%`;
      }

      clearMessage();
    };

    questions.forEach((question) => {
      question.querySelectorAll('input[type="radio"]').forEach((input) => {
        input.addEventListener("change", () => {
          question.querySelectorAll(".decision-option").forEach((option) => {
            option.classList.toggle(
              "selected",
              Boolean(option.querySelector('input[type="radio"]:checked'))
            );
          });

          refreshAnswered();
          clearMessage();
        });
      });
    });

    nextButton.addEventListener("click", () => {
      if (!selectedAnswer(questions[currentIndex])) {
        showMessage("Elige una acción antes de pasar a la siguiente habitación.");
        return;
      }

      showQuestion(currentIndex + 1);
    });

    previousButton.addEventListener("click", () => {
      showQuestion(currentIndex - 1);
    });

    submitButton.addEventListener("click", async () => {
      const answers = {};
      let incompleteIndex = -1;

      questions.forEach((question, index) => {
        const selected = selectedAnswer(question);

        if (!selected && incompleteIndex === -1) {
          incompleteIndex = index;
        }

        answers[question.dataset.scenarioId] = selected?.value || "";
      });

      if (incompleteIndex >= 0) {
        showQuestion(incompleteIndex);
        showMessage("Responde esta situación antes de evaluar tu EcoCasa.");
        return;
      }

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Evaluando impacto';

      try {
        const data = await submitResult(answers);
        layout?.classList.add("d-none");

        showResult(data, (container, feedbackItems) => {
          feedbackItems.forEach((item) => {
            const article = document.createElement("article");
            article.className = `decision-feedback-card ${item.correct ? "positive" : "improve"}`;

            const icon = document.createElement("i");
            icon.className = item.correct
              ? "bi bi-house-check-fill"
              : "bi bi-lightbulb-fill";

            const content = document.createElement("div");
            const title = document.createElement("strong");
            title.textContent = `${item.room}: ${item.correct ? "excelente decisión" : "puedes mejorar"}`;

            const chosen = document.createElement("p");
            chosen.textContent = `Elegiste: ${item.chosen}. ${item.explanation}`;
            content.append(title, chosen);

            if (!item.correct) {
              const recommendation = document.createElement("small");
              recommendation.textContent = `Recomendación: ${item.recommended}.`;
              content.append(recommendation);
            }

            article.append(icon, content);
            container.append(article);
          });
        });
      } catch (error) {
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-house-check-fill me-2"></i>Evaluar mi EcoCasa';
        showMessage(error.message);
      }
    });

    refreshAnswered();
    showQuestion(0);
  };

  if (gameType === "clasifica") {
    initializeClassification();
  }

  if (gameType === "trivia") {
    initializeTrivia();
  }

  if (gameType === "memorama") {
    initializeMemory();
  }

  if (gameType === "limpia-parque") {
    initializeCleanPark();
  }

  if (gameType === "ruta-reciclaje") {
    initializeRecyclingRoute();
  }

  if (gameType === "decisiones") {
    initializeDecisions();
  }
})();