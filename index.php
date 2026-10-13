
<?php
$n = 2;
$p = 1;
$run = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $processInput = $_POST["processes"] ?? "";
    $requesterInput = $_POST["requester"] ?? "";

    if (
        filter_var($processInput, FILTER_VALIDATE_INT) === false ||
        filter_var($requesterInput, FILTER_VALIDATE_INT) === false
    ) {
        $error = "Please enter valid whole numbers.";
    } else {
        $n = (int)$processInput;
        $p = (int)$requesterInput;

        if ($n < 2 || $n > 5) {
            $error = "Number of processes must be between 2 and 5.";
            $n = 2;
        } elseif ($p < 0 || $p >= $n) {
            $error = "Process ID must be between P0 and P" . ($n - 1) . ".";
        } else {
            $run = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ricart–Agrawala Simulator</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f7f5;
    color: #172b24;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
}

.container {
    max-width: 1000px;
    margin: 45px auto;
    padding: 0 20px;
}

h1 {
    font-size: 30px;
    margin: 0 0 10px;
}

.subtitle {
    color: #718078;
    margin: 0 0 30px;
    line-height: 1.6;
}

.layout {
    display: grid;
    grid-template-columns: 0.9fr 1.2fr;
    gap: 22px;
    align-items: start;
}

.card {
    min-width: 0;
    background: #ffffff;
    border: 1px solid #e0e9e3;
    border-radius: 13px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(20, 60, 40, 0.04);
}

h2 {
    font-size: 18px;
    margin: 0 0 12px;
}

.description {
    color: #718078;
    line-height: 1.6;
    margin: 0 0 25px;
}

label {
    display: block;
    font-weight: bold;
    margin: 18px 0 9px;
}

input {
    width: 100%;
    padding: 13px;
    border: 1px solid #d7e1da;
    border-radius: 7px;
    font-size: 14px;
    background: #ffffff;
    color: #172b24;
}

input:focus {
    outline: 2px solid #b9e4ce;
    border-color: #17734b;
}

.hint {
    color: #849188;
    font-size: 12px;
    margin: 8px 0 0;
}

button {
    width: 100%;
    margin-top: 25px;
    padding: 14px;
    background: #176c49;
    color: #ffffff;
    border: none;
    border-radius: 7px;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
}

button:hover {
    background: #105638;
}

.output-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e8eee9;
}

.output-heading h2 {
    margin: 0;
}

.tag,
.message-tag {
    background: #e8f5ec;
    color: #176c49;
    padding: 7px 9px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: bold;
}

.stats {
    background: #f7f9f7;
    border: 1px solid #e5ebe6;
    border-radius: 8px;
    padding: 16px;
    margin: 20px 0;
}

.stat-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin: 8px 0;
}

.stat-row span {
    color: #718078;
}

.section-title {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 22px 0 12px;
    font-weight: bold;
}

.number {
    background: #e7f4eb;
    color: #176c49;
    padding: 6px 8px;
    border-radius: 5px;
}

.message {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 14px;
    border: 1px solid #e0e9e3;
    border-radius: 7px;
    margin-bottom: 10px;
}

.error {
    color: #a52828;
    background: #fff0f0;
    border: 1px solid #f0cccc;
    border-radius: 7px;
    padding: 12px;
    margin: 15px 0;
    line-height: 1.5;
}

.summary {
    margin-top: 22px;
    background: #eff8f1;
    border: 1px solid #d5eadb;
    border-radius: 8px;
    padding: 16px;
    line-height: 1.9;
}

.summary h3 {
    margin: 0 0 10px;
    font-size: 14px;
    color: #176c49;
}

.summary p {
    margin: 5px 0;
}

.check {
    color: #168252;
    font-weight: bold;
    margin-right: 7px;
}

.empty {
    color: #718078;
    line-height: 1.8;
    padding: 20px 0;
}

footer {
    border-top: 1px solid #e0e9e3;
    margin-top: 42px;
    padding-top: 20px;
    color: #718078;
    font-size: 12px;
}

@media (max-width: 700px) {
    .container {
        margin: 25px auto;
    }

    .layout {
        grid-template-columns: 1fr;
    }

    h1 {
        font-size: 25px;
    }

    .card {
        padding: 20px;
    }
}
</style>
</head>

<body>
<div class="container">

    <h1>Ricart–Agrawala Simulator</h1>

    <p class="subtitle">
        Explore distributed mutual exclusion through a simple process communication simulation.
    </p>

    <div class="layout">

        <div class="card">
            <h2>Simulation Setup</h2>

            <p class="description">
                Configure the processes and select which process requests access to the critical section.
            </p>

            <?php if ($error !== ""): ?>
                <div class="error">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="processes">Number of Processes</label>

                <input
                    type="number"
                    id="processes"
                    name="processes"
                    min="2"
                    max="5"
                    value="<?php echo htmlspecialchars((string)$n, ENT_QUOTES, "UTF-8"); ?>"
                    required
                >

                <p class="hint">Choose between 2 and 5 processes.</p>

                <label for="requester">Requesting Process ID</label>

                <input
                    type="number"
                    id="requester"
                    name="requester"
                    min="0"
                    max="<?php echo $n - 1; ?>"
                    value="<?php echo htmlspecialchars((string)$p, ENT_QUOTES, "UTF-8"); ?>"
                    required
                >

                <p class="hint">Process IDs start from P0.</p>

                <button type="submit">&#9654; &nbsp; Run Simulation</button>
            </form>
        </div>

        <div class="card">

            <div class="output-heading">
                <h2>Simulation Output</h2>
                <span class="tag">OUTPUT</span>
            </div>

            <?php if ($run): ?>

                <div class="stats">
                    <div class="stat-row">
                        <span>Total Processes</span>
                        <strong><?php echo $n; ?></strong>
                    </div>

                    <div class="stat-row">
                        <span>Requesting Process</span>
                        <strong>P<?php echo $p; ?></strong>
                    </div>

                    <div class="stat-row">
                        <span>Simulation Status</span>
                        <strong>Completed</strong>
                    </div>
                </div>

                <div class="section-title">
                    <span class="number">01</span>
                    REQUEST Messages
                </div>

                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php if ($i !== $p): ?>
                        <div class="message">
                            <strong>
                                P<?php echo $p; ?> &rarr; P<?php echo $i; ?>
                            </strong>
                            <span class="message-tag">REQUEST</span>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>

                <div class="section-title">
                    <span class="number">02</span>
                    REPLY Messages
                </div>

                <?php for ($i = 0; $i < $n; $i++): ?>
                    <?php if ($i !== $p): ?>
                        <div class="message">
                            <strong>
                                P<?php echo $i; ?> &rarr; P<?php echo $p; ?>
                            </strong>
                            <span class="message-tag">REPLY</span>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>

                <div class="summary">
                    <h3>Simulation Summary</h3>

                    <p><span class="check">&#10003;</span> All REPLY messages received.</p>
                    <p><span class="check">&#10003;</span> P<?php echo $p; ?> enters the Critical Section.</p>
                    <p><span class="check">&#10003;</span> P<?php echo $p; ?> uses the shared resource.</p>
                    <p><span class="check">&#10003;</span> P<?php echo $p; ?> exits the Critical Section.</p>
                </div>

            <?php else: ?>

                <div class="empty">
                    Enter the number of processes and select a requesting process.
                    Click <strong>Run Simulation</strong> to display the messages here.
                </div>

            <?php endif; ?>

        </div>
    </div>

    <footer>Ricart–Agrawala Simulator</footer>

</div>
</body>
</html>
