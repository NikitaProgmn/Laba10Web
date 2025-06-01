<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Бронювання кімнат в готелі</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/daypilot-all.min.js"></script>

    <style>
        #dp {
            width: 100%;
            height: 600px;
            margin: 20px auto;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        header, footer {
            background-color: #f0f0f0;
            padding: 10px 20px;
            text-align: center;
        }
        footer address {
            font-style: normal;
        }

        .scheduler_default_rowheader_inner {
            border-right: 1px solid #ccc;
        }
        .scheduler_default_rowheader.scheduler_default_rowheadercol2 {
            background: #fff;
        }
        .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            background-color: transparent;
            border-left: 5px solid #1a9d13;
        }
        .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #ea3624;
        }
        .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #f9ba25;
        }
    </style>
</head>
<body>

<header>
    <h1>HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
</header>

<main>
    <div id="dp"></div>
</main>

<footer>
    <address>(с) Автор: студент ПЗіС-24005м, Мошнін Микита Андрійович</address>
</footer>

<script>
    var dp = new DayPilot.Scheduler("dp");

    dp.startDate = DayPilot.Date.today().firstDayOfMonth();
    dp.days = DayPilot.Date.today().daysInMonth();
    dp.scale = "Day";
    dp.timeHeaders = [
        { groupBy: "Month", format: "MMMM yyyy" },
        { groupBy: "Day", format: "d" }
    ];

    dp.rowHeaderColumns = [
        { title: "Кімната", width: 80 },
        { title: "Місткість", width: 80 },
        { title: "Статус", width: 80 }
    ];

    dp.onBeforeResHeaderRender = function(args) {
        var beds = function(count) {
            return count + " ліжко";
        };
        args.resource.columns[0].html = args.resource.name;
        args.resource.columns[1].html = beds(args.resource.capacity);
        args.resource.columns[2].html = args.resource.status;

        switch (args.resource.status.toLowerCase()) {
            case "брудна":
                args.resource.cssClass = "status_dirty";
                break;
            case "прибирається":
                args.resource.cssClass = "status_cleanup";
                break;
        }
    };

    dp.onTimeRangeSelected = function(args) {
        var modal = new DayPilot.Modal();
        modal.closed = function() {
            dp.clearSelection();
            var data = this.result;
            if (data && data.result === "OK") {
                loadEvents();
            }
        };
        modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);
    };

    // 📌 Додано: Редагування події
    dp.onEventClick = function(args) {
        var modal = new DayPilot.Modal();
        modal.closed = function() {
            var data = this.result;
            if (data && data.result === "OK") {
                loadEvents();
            }
        };
        modal.showUrl("edit.php?id=" + args.e.id());
    };

    function loadResources() {
        $.post("backend_rooms.php", function(data) {
            dp.resources = data;
            dp.init();
            loadEvents();
        });
    }

    function loadEvents() {
        let start = dp.visibleStart();
        let end = dp.visibleEnd();
        $.post("backend_events.php", {
            start: start.toString(),
            end: end.toString()
        }, function(data) {
            dp.events.list = data;
            dp.update();
        });
    }

    loadResources();
</script>

</body>
</html>
