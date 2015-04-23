import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;
import org.flightgear.telnetconnection.FGTelnetConnection;

public class FGReportServlet extends HttpServlet{
	private FGTelnetConnection conn = null;

	public void doPost(HttpServletRequest request, HttpServletResponse response)
		throws IOException, ServletException {
		response.setContentType("text/html;charset=UTF-8");
		PrintWriter out = response.getWriter();

		this.conn = new FGTelnetConnection("192.168.1.101", 5400);
		out.print("<pre>");
		out.println("Real-world Time:           "
			+ this.conn.get("/sim/time/real/string"));
		out.println("UTC Time:                  "
			+ this.conn.get("/sim/time/utc/string"));
		out.println("Local Time:                "
			+ this.conn.get("/instrumentation/clock/local-short-string"));
		out.println("Aircraft:                  "
			+ this.conn.get("/sim/description"));
		out.printf ("Position (lat lon):        %.6f %.6f\n",
			this.conn.getFloat("/position/latitude-deg"),
			this.conn.getFloat("/position/longitude-deg"));
		out.printf ("Total Distance (nmi):      %.2f\n",
			this.conn.getFloat("/autopilot/route-manager/total-distance"));
		out.printf ("Distance Remaining (nmi):  %.2f\n",
			this.conn.getFloat("/autopilot/route-manager/distance-remaining-nm"));
		out.println("Flight Time:               "
			+ this.conn.get("/autopilot/route-manager/flight-time-string"));
		out.println("Time Remaining:            "
			+ this.conn.get("/autopilot/route-manager/ete-string"));
		out.printf ("Fuel:                      %.2f%%\n",
			this.conn.getFloat("/consumables/fuel/total-fuel-norm") * 100.0);
		out.println("Paused:                    "
			+ ((this.conn.getBoolean("/sim/freeze/clock") && this.conn.getBoolean("/sim/freeze/master")) ? "True" : "False"));
		out.println("Crashed:                   "
			+ (this.conn.getBoolean("/sim/crashed") ? "True" : "False"));
		out.print("</pre>");

		this.conn.close();
	}

	public void doGet(HttpServletRequest request, HttpServletResponse response)
		throws IOException, ServletException {
		doPost(request, response);
	}
}

