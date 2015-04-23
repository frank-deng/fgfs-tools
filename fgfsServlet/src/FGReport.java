import org.flightgear.telnetconnection.FGTelnetConnection;
import java.io.IOException;

public class FGReport {

	private FGTelnetConnection conn;

	public FGReport(String host, int port) {
		try {
			this.conn = new FGTelnetConnection(host, port);

			System.out.println("Real-world Time:           "
				+ this.conn.get("/sim/time/real/string"));
			System.out.println("UTC Time:                  "
				+ this.conn.get("/sim/time/utc/string"));
			System.out.println("Local Time:                "
				+ this.conn.get("/instrumentation/clock/local-short-string"));
			System.out.println("Aircraft:                  "
				+ this.conn.get("/sim/description"));
			System.out.printf ("Position (lat lon):        %.6f %.6f\n",
				this.conn.getFloat("/position/latitude-deg"),
				this.conn.getFloat("/position/longitude-deg"));
			System.out.printf ("Total Distance (nmi):      %.2f\n",
				this.conn.getFloat("/autopilot/route-manager/total-distance"));
			System.out.printf ("Distance Remaining (nmi):  %.2f\n",
				this.conn.getFloat("/autopilot/route-manager/distance-remaining-nm"));
			System.out.println("Flight Time:               "
				+ this.conn.get("/autopilot/route-manager/flight-time-string"));
			System.out.println("Time Remaining:            "
				+ this.conn.get("/autopilot/route-manager/ete-string"));
			System.out.printf ("Fuel:                      %.2f%%\n",
				this.conn.getFloat("/consumables/fuel/total-fuel-norm") * 100.0);
			System.out.println("Paused:                    "
				+ ((this.conn.getBoolean("/sim/freeze/clock") && this.conn.getBoolean("/sim/freeze/master")) ? "True" : "False"));
			System.out.println("Crashed:                   "
				+ (this.conn.getBoolean("/sim/crashed") ? "True" : "False"));

			this.conn.close();
		}catch(IOException e) {
			System.out.println(e);
			System.exit(1);
		}
	}

	public static void main(String[] args) {
		if (args.length >= 2) {
			FGReport fgReport = new FGReport(args[0], Integer.parseInt(args[1]));
		} else {
			System.out.println("Usage: java FGReport host port");
		}
	}

}

